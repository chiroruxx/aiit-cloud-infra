<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PublicKey;
use Illuminate\Database\Eloquent\Factories\Factory;

class PublicKeyFactory extends Factory
{
    protected $model = PublicKey::class;

    public function definition(): array
    {
        return [
            'hash' => $this->faker->unique()->sha256,
            'content' => $this->faker->text(700)
        ];
    }
}
