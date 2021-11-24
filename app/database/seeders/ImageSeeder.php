<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Image;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    public function run(): void
    {
        Image::create([
            'name' => 'centos:8',
            'docker_image_name' => '10.10.10.1:5000/private/c8-systemd-ssh',
        ]);
        Image::create([
            'name' => 'ubuntu:20',
            'docker_image_name' => '10.10.10.1:5000/private/u20-sshd',
        ]);
    }
}
