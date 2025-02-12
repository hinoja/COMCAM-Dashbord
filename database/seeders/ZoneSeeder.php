<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Zone::factory()->create([
            'name' => 'Zone 1',
        ]);

        \App\Models\Zone::factory()->create([
            'name' => 'Zone 2',
        ]);

        \App\Models\Zone::factory()->create([
            'name' => 'Zone 3',
        ]);
    }
}
