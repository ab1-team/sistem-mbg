<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AccountSeeder::class, // Establishing Level 1-3 categories
            MaterialSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            DapurSeeder::class,    // Optional: Creates default Dapurs
        ]);
    }
}
