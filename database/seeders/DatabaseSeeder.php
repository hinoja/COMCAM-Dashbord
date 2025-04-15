<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
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
            RoleSeeder::class,
        ]);

        if (!app()->environment('production')) {
            User::factory(2)->create();
        }
        User::factory()->create([
            'name' => 'Comcam',
            'email' => 'comcam@gmail.com',
            'role_id' => 2,
            'password' => Hash::make("password")
        ]);
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'role_id' => 1,
            'password' => Hash::make("p@ssword")
        ]);
        User::factory()->create([
            'name' => 'Delmas Lemoula',
            'email' => 'delmas.lemoula@yahoo.com',
            'role_id' => 2,
            'password' => Hash::make("password")
        ]);
    }
}
