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
 * @property int $cpus
 * @property string $memory_size
 * @property string $vm
 * @property string $container_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Instance $instance
 * @method static \Illuminate\Database\Eloquent\Builder|Container newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Container newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Container query()
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereContainerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereCpus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereInstanceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereMemorySize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereVm($value)
 * @mixin \Eloquent
 */
class Container extends Model
{
    use HasFactory;

    protected $fillable = ['container_id', 'cpus', 'memory_size', 'vm'];

    public function instance(): BelongsTo
    {
        return $this->belongsTo(Instance::class);
    }
}
