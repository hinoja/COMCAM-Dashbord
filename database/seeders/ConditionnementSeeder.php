<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConditionnementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Conditionnemment::factory()->create([
            'designation' => 'Conventionel Débit',
            'code' => 'CDT',
        ]);

        \App\Models\Conditionnemment::factory()->create([
            'designation' => 'Conteneur Débit',
            'code' => 'CD',
        ]);

        \App\Models\Conditionnemment::factory()->create([
            'designation' => 'Conventionel Grume',
            'code' => 'CG',
        ]);

        \App\Models\Conditionnemment::factory()->create([
            'designation' => 'Conteneur Grume',
            'code' => 'CTG',
        ]);
    }
}
