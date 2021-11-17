<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Container
 *
 * @property int $id
 * @property int $instance_id
 * @property int $public_key_id
 * @property int|null $machine_id
 * @property int $cpus
 * @property int $memory_size
 * @property int $storage_size
 * @property string|null $ip
 * @property string|null $container_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Instance $instance
 * @property-read \App\Models\Machine|null $machine
 * @property-read \App\Models\PublicKey $publicKey
 * @method static \Database\Factories\ContainerFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Container newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Container newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Container query()
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereContainerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereCpus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereInstanceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereMachineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereMemorySize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container wherePublicKeyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereStorageSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Container extends Model
{
    use HasFactory;

    protected $fillable = ['container_id', 'cpus', 'memory_size', 'storage_size', 'ip'];

    public function instance(): BelongsTo
    {
        return $this->belongsTo(Instance::class);
    }

    public function publicKey(): BelongsTo
    {
        return $this->belongsTo(PublicKey::class);
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function setIp(): self
    {
        $availableIps = $this->machine->getAvailableIps();

        $this->ip = $availableIps[array_rand($availableIps)];

        return $this;
    }
}
