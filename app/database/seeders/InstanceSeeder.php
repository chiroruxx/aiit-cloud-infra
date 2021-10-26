<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Instance;
use Illuminate\Database\Seeder;

class InstanceSeeder extends Seeder
{
    public function run(): void
    {
        Instance::factory()->count(5)->create();
    }
}
