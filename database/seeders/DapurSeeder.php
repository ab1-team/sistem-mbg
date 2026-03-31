<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DapurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('dapurs')->insertOrIgnore([
            [
                'code' => 'DPR-JKT-001',
                'name' => 'Dapur Utama Jakarta',
                'address' => 'Jl. Kebagusan No. 1, Jakarta Selatan',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'capacity_portions' => 500,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'DPR-BOG-001',
                'name' => 'Dapur Bogor',
                'address' => 'Jl. Raya Bogor No. 10',
                'city' => 'Bogor',
                'province' => 'Jawa Barat',
                'capacity_portions' => 300,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
