<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Machine
 *
 * @property int $id
 * @property string $name
 * @property string $ip_range
 * @property string $queue_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Machine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Machine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Machine query()
 * @method static \Illuminate\Database\Eloquent\Builder|Machine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Machine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Machine whereIpRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Machine whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Machine whereQueueName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Machine whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Machine extends Model
{
    // ランダムデータを作成しないので HasFactory は使用しない

    protected $fillable = ['name', 'ip_range', 'queue_name'];
}
