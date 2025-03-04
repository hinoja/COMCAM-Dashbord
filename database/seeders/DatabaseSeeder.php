<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            FormeSeeder::class,
            EssenceSeeder::class,
            TypeSeeder::class,
            ConditionnementSeeder::class,
            ExportateurSeeder::class,
            ZoneSeeder::class,
        ]);

        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Comcam',
            'email' => 'comcam@gmail.com',
            'password'=>Hash::make("password")
        ]);

    }
}
