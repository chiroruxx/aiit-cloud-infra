<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Container;
use App\Models\Instance;
use App\Models\PublicKey;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContainerFactory extends Factory
{
    protected $model = Container::class;

    public function definition(): array
    {
        return [
            'instance_id' => Instance::factory(),
            'public_key_id' => PublicKey::factory(),
            'cpus' => 1,
            'memory_size' => '512m',
            'vm' => $this->faker->optional->randomElement(['vm1', 'vm2']),
            'container_id' => $this->faker->optional->sha256,
        ];
    }
}
