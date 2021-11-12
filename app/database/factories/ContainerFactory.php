<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Container;
use App\Models\Instance;
use App\Models\Machine;
use App\Models\PublicKey;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContainerFactory extends Factory
{
    protected $model = Container::class;

    public function definition(): array
    {
        $machines = Machine::pluck('id');
        return [
            'instance_id' => Instance::factory(),
            'public_key_id' => PublicKey::factory(),
            'machine_id' => $this->faker->optional->randomElement($machines),
            'cpus' => 1,
            'memory_size' => '512m',
            'ip' => $this->faker->optional->ipv4,
            'container_id' => $this->faker->optional->sha256,
        ];
    }
}
