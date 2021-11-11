<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PublicKey;
use Illuminate\Database\Seeder;

class PublicKeySeeder extends Seeder
{
    public function run(): void
    {
        PublicKey::factory()->state(['hash' => 'default'])->create();
    }
}
