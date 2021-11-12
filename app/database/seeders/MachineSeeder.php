<?php

namespace Database\Seeders;

use App\Models\Machine;
use Illuminate\Database\Seeder;

class MachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Machine::create([
            'name' => 'vm1',
            'ip_range' => '10.10.10.0/16',
        ]);
        Machine::create([
            'name' => 'vm2',
            'ip_range' => '10.10.20.0/16',
        ]);
    }
}
