<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(MachineSeeder::class);
        $this->call(ImageSeeder::class);

        if ($this->command->confirm('ダミーデータを生成しますか？')) {
            $this->call([
                PublicKeySeeder::class,
                ContainerSeeder::class,
            ]);
        }
    }
}
