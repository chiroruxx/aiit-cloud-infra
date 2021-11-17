<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\MachineStatistic
 *
 * @property int $machine_id
 * @property string $name
 * @property int $max_cpu_count
 * @property int $max_memory_size
 * @property string|null $memory_used
 * @property string|null $memory_free
 * @property int $max_storage_size
 * @property string|null $storage_used
 * @property string|null $storage_free
 * @property string|null $score
 * @property-read \App\Models\Machine $machine
 * @method static \Illuminate\Database\Eloquent\Builder|MachineStatistic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MachineStatistic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MachineStatistic query()
 * @method static \Illuminate\Database\Eloquent\Builder|MachineStatistic whereMachineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MachineStatistic whereMaxCpuCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MachineStatistic whereMaxMemorySize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MachineStatistic whereMaxStorageSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MachineStatistic whereMemoryFree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MachineStatistic whereMemoryUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MachineStatistic whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MachineStatistic whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MachineStatistic whereStorageFree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MachineStatistic whereStorageUsed($value)
 * @mixin \Eloquent
 */
class MachineStatistic extends Model
{
    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public static function determineMachine(int $cpus, int $memorySize, int $storageSize): Machine
    {
        return self::where('max_cpu_count', '>=', $cpus)
            ->where('memory_free', '>=', $memorySize)
            ->where('storage_free', '>=', $storageSize)
            ->orderBy('score')
            ->firstOrFail(['machine_id'])
            ->machine;
    }
}
