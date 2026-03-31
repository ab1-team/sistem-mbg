<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@mbg.yayasan'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'uuid' => (string) Str::uuid(),
                'is_active' => true,
            ]
        );

        $admin->assignRole('superadmin');
    }
}
