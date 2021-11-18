<?php

declare(strict_types=1);

namespace App\Models;

use App\ByteSize;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use LogicException;

/**
 * App\Models\Instance
 *
 * @property int $id
 * @property string $name
 * @property string $hash
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Container|null $container
 * @property-read int $cpus
 * @property-read string|null $ip
 * @property-read string $key
 * @property-read string $memory_size
 * @method static \Database\Factories\InstanceFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Instance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Instance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Instance query()
 * @method static \Illuminate\Database\Eloquent\Builder|Instance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instance whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instance whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Instance extends Model
{
    use HasFactory;
    use Hashable;

    private const STATUS_INITIALIZING = 'initializing';
    private const STATUS_STARTING = 'starting';
    private const STATUS_RUNNING = 'running';
    private const STATUS_TERMINATING = 'terminating';
    private const STATUS_TERMINATED = 'terminated';
    private const STATUS_HALTING = 'halting';
    private const STATUS_HALTED = 'halted';

    protected $appends = ['image', 'cpus', 'memory_size', 'key', 'ip'];
    protected $fillable = ['name', 'hash', 'status'];
    protected $hidden = ['id', 'container'];
    protected $with = ['container.publicKey'];

    public static function getChangeableStatuses(): array
    {
        return [
            self::STATUS_HALTED,
            self::STATUS_RUNNING,
        ];
    }

    public function container(): HasOne
    {
        return $this->hasOne(Container::class);
    }

    public function getImageAttribute(): string
    {
        return $this->container->image->name;
    }

    public function getCpusAttribute(): int
    {
        return $this->container->cpus;
    }

    public function getMemorySizeAttribute(): string
    {
        return (new ByteSize($this->container->memory_size))->getWithUnit();
    }

    public function getKeyAttribute(): string
    {
        return $this->container->publicKey->hash;
    }

    public function getIpAttribute(): string|null
    {
        return $this->container->ip;
    }

    public function getRouteKeyName(): string
    {
        return 'hash';
    }

    public static function initialize(
        string $instanceName,
        string $imageName,
        string $publicKeyHash,
        int $cpus,
        int $memorySize,
        int $storageSize
    ): self {
        $container = new Container();
        $container->fill([
            'cpus' => $cpus,
            'memory_size' => $memorySize,
            'storage_size' => $storageSize,
        ]);

        $publicKey = PublicKey::whereHash($publicKeyHash)->firstOrFail(['id']);
        $container->publicKey()->associate($publicKey);

        $image = Image::whereName($imageName)->firstOrFail(['id']);
        $container->image()->associate($image);

        return DB::transaction(function () use ($instanceName, $container): self {
            $instance = self::create([
                'name' => $instanceName,
                'hash' => self::generateActiveHash(),
                'status' => self::STATUS_INITIALIZING
            ]);

            $container->instance()->associate($instance);
            $container->save();

            return $instance;
        });
    }

    public function start(): self
    {
        $this->status = self::STATUS_STARTING;
        $this->save();

        return $this;
    }

    public function run(string $containerId): self
    {
        $this->status = self::STATUS_RUNNING;

        $this->container->container_id = $containerId;

        DB::transaction(function (): void {
            $this->container->save();
            $this->save();
        });

        return $this;
    }

    public function terminate(): self
    {
        $this->status = self::STATUS_TERMINATING;
        $this->save();

        return $this;
    }

    public function completeTerminate(): self
    {
        $this->status = self::STATUS_TERMINATED;
        $this->container->fill([
            'container_id' => null,
            'ip' => null,
        ]);
        $this->container->machine()->dissociate();

        DB::transaction(function (): void {
            $this->container->save();
            $this->save();
        });

        return $this;
    }

    public function halt(): self
    {
        $this->status = self::STATUS_HALTING;
        $this->save();

        return $this;
    }

    public function completeHalt(): self
    {
        $this->status = self::STATUS_HALTED;
        $this->save();

        return $this;
    }

    public function restart(): self
    {
        $this->status = self::STATUS_STARTING;
        $this->save();

        return $this;
    }

    public function completeRestart(): self
    {
        $this->status = self::STATUS_RUNNING;
        $this->save();

        return $this;
    }

    public function updateName(?string $name): void
    {
        $this->name = $name;
        $this->save();
    }

    public function canChangeStatusTo(string $to): bool
    {
        return match ($to) {
            self::STATUS_HALTED => $this->status === self::STATUS_RUNNING,
            self::STATUS_RUNNING => $this->status === self::STATUS_HALTED,
            default => throw new LogicException("Status {$to} is not supported."),
        };
    }
}
