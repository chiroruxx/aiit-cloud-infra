<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Container;
use Illuminate\Database\Seeder;

class ContainerSeeder extends Seeder
{
    public function run(): void
    {
        Container::factory()->count(3)->create();
    }
}
