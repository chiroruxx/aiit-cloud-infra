<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Instance;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Instance::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'hash' => $this->faker->unique()->sha256,
            'status' => $this->faker->randomElement(['pending', 'running', 'stopped'])
        ];
    }
}
