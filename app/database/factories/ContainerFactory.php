<?php

declare(strict_types=1);

namespace Database\Factories;

use App\ByteSize;
use App\Models\Container;
use App\Models\Image;
use App\Models\Instance;
use App\Models\Machine;
use App\Models\PublicKey;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContainerFactory extends Factory
{
    protected $model = Container::class;

    public function definition(): array
    {
        $images = Image::pluck('id');
        $machines = Machine::pluck('id');
        $memorySize = "{$this->faker->numberBetween(0, 1024)}m";
        $storageSize = "{$this->faker->numberBetween(100, 200)}g";

        return [
            'instance_id' => Instance::factory(),
            'public_key_id' => PublicKey::factory(),
            'image_id' => $this->faker->randomElement($images),
            'machine_id' => $this->faker->optional->randomElement($machines),
            'cpus' => 1,
            'memory_size' => ByteSize::createWithUnit($memorySize)->getValue(),
            'storage_size' => ByteSize::createWithUnit($storageSize)->getValue(),
            'ip' => $this->faker->optional->ipv4,
            'container_id' => $this->faker->optional->sha256,
        ];
    }
}
