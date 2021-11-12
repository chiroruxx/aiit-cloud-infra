<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Machine
 *
 * @property int $id
 * @property string $name
 * @property int $max_cpu_count
 * @property string $ip_range
 * @property string $queue_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Container[] $containers
 * @property-read int|null $containers_count
 * @method static \Illuminate\Database\Eloquent\Builder|Machine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Machine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Machine query()
 * @method static \Illuminate\Database\Eloquent\Builder|Machine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Machine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Machine whereIpRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Machine whereMaxCpuCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Machine whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Machine whereQueueName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Machine whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Machine extends Model
{
    // ランダムデータを作成しないので HasFactory は使用しない

    protected $fillable = ['name', 'max_cpu_count', 'ip_range', 'queue_name'];

    public function containers(): HasMany
    {
        return $this->hasMany(Container::class);
    }

    public function getAvailableIps(): array
    {
        $all = $this->getAllIps();
        // ネットワークアドレスとブロードキャストアドレスを除く
        array_shift($all);
        array_pop($all);

        $containerIps = $this->containers->pluck('ip');

        return collect($all)->diff($containerIps)->all();
    }

    protected function getAllIps(): array
    {
        [$ip, $mask] = explode('/', $this->ip_range);

        $min = (ip2long($ip)) & ((-1 << (32 - (int)$mask)));
        $max = (ip2long($ip)) + pow(2, (32 - (int)$mask)) - 1;
        $ips = range($min, $max);

        return array_map('long2ip', $ips);
    }
}
