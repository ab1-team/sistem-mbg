<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'ahli_gizi', 'display_name' => 'Ahli Gizi', 'description' => 'Merancang menu & gizi'],
            ['name' => 'kepala_dapur', 'display_name' => 'Kepala Dapur', 'description' => 'Approve/reject rancangan menu'],
            ['name' => 'logistik', 'display_name' => 'Logistik', 'description' => 'Kelola stok, generate PO, terima barang'],
            ['name' => 'koki', 'display_name' => 'Koki', 'description' => 'Eksekusi masak harian'],
            ['name' => 'admin_yayasan', 'display_name' => 'Admin Yayasan', 'description' => 'Review & forward PO ke supplier'],
            ['name' => 'finance_yayasan', 'display_name' => 'Finance Yayasan', 'description' => 'Kelola keuangan, kalkulasi bagi hasil'],
            ['name' => 'supplier', 'display_name' => 'Supplier', 'description' => 'Terima PO, kirim barang'],
            ['name' => 'investor', 'display_name' => 'Investor', 'description' => 'Dashboard performa, withdrawal'],
            ['name' => 'superadmin', 'display_name' => 'Super Admin', 'description' => 'Full access'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                [
                    'display_name' => $role['display_name'],
                    'description' => $role['description'],
                    'guard_name' => 'web',
                ]
            );
        }
    }
}
