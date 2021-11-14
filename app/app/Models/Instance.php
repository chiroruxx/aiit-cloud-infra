<?php

declare(strict_types=1);

namespace App\Models;

use App\ByteSize;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    private const STATUS_INITIALIZING = 'initializing';
    private const STATUS_STARTING = 'starting';
    private const STATUS_RUNNING = 'running';
    private const STATUS_TERMINATING = 'terminating';
    private const STATUS_TERMINATED = 'terminated';

    use HasFactory;
    use Hashable;

    protected $appends = ['cpus', 'memory_size', 'key', 'ip'];
    protected $fillable = ['name', 'hash', 'status'];
    protected $hidden = ['id', 'container'];
    protected $with = ['container.publicKey'];

    public function container(): HasOne
    {
        return $this->hasOne(Container::class);
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

    public static function initialize(string $instanceName, string $publicKeyHash, int $cpus, int $memorySize): self
    {
        $publicKey = PublicKey::whereHash($publicKeyHash)->firstOrFail(['id']);

        $container = new Container();
        $container->fill([
            'cpus' => $cpus,
            'memory_size' => $memorySize,
        ]);
        $container->publicKey()->associate($publicKey);

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

        DB::transaction(function (): void {
            $this->container->save();
            $this->save();
        });

        return $this;
    }
}
