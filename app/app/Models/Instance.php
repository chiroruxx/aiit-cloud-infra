<?php

declare(strict_types=1);

namespace App\Models;

use App\ByteSize;
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
    private const STATUS_FAILED = 'failed';

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

    public static function toInitializing(string $instanceName): self
    {
        return self::create([
            'name' => $instanceName,
            'hash' => self::generateActiveHash(),
            'status' => self::STATUS_INITIALIZING
        ]);
    }

    public function toStarting(): self
    {
        return $this->updateStatus(self::STATUS_STARTING);
    }

    public function toRunning(): self
    {
        return $this->updateStatus(self::STATUS_RUNNING);
    }

    public function toTerminating(): self
    {
        return $this->updateStatus(self::STATUS_TERMINATING);
    }

    public function toTerminated(): self
    {
        return $this->updateStatus(self::STATUS_TERMINATED);
    }

    public function toHalting(): self
    {
        return $this->updateStatus(self::STATUS_HALTING);
    }

    public function toHalted(): self
    {
        return $this->updateStatus(self::STATUS_HALTED);
    }

    public function toFailed(): self
    {
        return $this->updateStatus(self::STATUS_FAILED);
    }

    public function updateName(?string $name): self
    {
        $this->name = $name;
        $this->save();

        return $this;
    }

    public function canChangeStatusTo(string $to): bool
    {
        return match ($to) {
            self::STATUS_HALTED => $this->status === self::STATUS_RUNNING,
            self::STATUS_RUNNING => $this->status === self::STATUS_HALTED,
            self::STATUS_TERMINATING => in_array($this->status, [self::STATUS_RUNNING, self::STATUS_HALTED], true),
            default => throw new LogicException("Status {$to} is not supported."),
        };
    }

    public function canTerminate(): bool
    {
        return $this->canChangeStatusTo(self::STATUS_TERMINATING);
    }

    protected function updateStatus(string $status): self
    {
        $this->status = $status;
        $this->save();

        return $this;
    }
}
