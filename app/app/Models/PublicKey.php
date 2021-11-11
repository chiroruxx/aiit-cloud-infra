<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

/**
 * App\Models\PublicKey
 *
 * @property int $id
 * @property string $hash
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\PublicKeyFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicKey query()
 * @method static \Illuminate\Database\Eloquent\Builder|PublicKey whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicKey whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PublicKey whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PublicKey extends Model
{
    use HasFactory;
    use Hashable;

    protected $fillable = ['content', 'hash'];
    // idはRDBとLaravelでしか使用しないのでレスポンスに含めない
    protected $hidden = ['id'];
}
