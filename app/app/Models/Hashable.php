<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Str;

/**
 * @method static self whereHash($value)
 * @method bool exists()
 */
trait Hashable
{
    public static function generateActiveHash(): string
    {
        $hash = Str::random(8);

        if (self::whereHash($hash)->exists()) {
            return self::generateActiveHash();
        }

        return $hash;
    }

}
