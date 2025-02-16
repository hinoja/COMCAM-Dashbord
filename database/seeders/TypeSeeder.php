<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Type::factory()->create([
            'designation' => 'Sciage',
            'code' => '5N',
            'forme_id' => 1,
        ]);

        Type::factory()->create([
            'designation' => 'Placage',
            'code' => '6.1',
            'forme_id' => 2,
        ]);

        Type::factory()->create([
            'designation' => 'Contreplaquet',
            'code' => '6.2',
            'forme_id' => 2,
        ]);

        Type::factory()->create([
            'designation' => 'PS',
            'code' => 'PS',
            'forme_id' => 3,
        ]);
    }
}
