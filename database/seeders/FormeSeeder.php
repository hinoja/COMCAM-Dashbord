<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FormeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Forme::factory()->create([
            'designation' => 'Grume',
        ]);

        \App\Models\Forme::factory()->create([
            'designation' => 'Débité',
        ]);

        \App\Models\Forme::factory()->create([
            'designation' => 'PS',
        ]);
    }
}
