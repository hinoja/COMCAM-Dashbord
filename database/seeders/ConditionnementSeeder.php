<?php

namespace Database\Seeders;



use Illuminate\Database\Seeder;
use App\Models\Conditionnemment;


class ConditionnementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Conditionnemment::factory()->create([
            'designation' => 'Conventionel Débit',
            'code' => 'CDT',
        ]);

        Conditionnemment::factory()->create([
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
