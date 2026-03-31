<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Menu Module
            ['name' => 'menu.view', 'display_name' => 'View Menu', 'module' => 'menu'],
            ['name' => 'menu.create', 'display_name' => 'Create Menu', 'module' => 'menu'],
            ['name' => 'menu.approve', 'display_name' => 'Approve Menu', 'module' => 'menu'],

            // PO Module
            ['name' => 'po.view', 'display_name' => 'View PO', 'module' => 'po'],
            ['name' => 'po.create', 'display_name' => 'Create PO', 'module' => 'po'],
            ['name' => 'po.review', 'display_name' => 'Review PO', 'module' => 'po'],
            ['name' => 'po.assign', 'display_name' => 'Assign Supplier', 'module' => 'po'],

            // Warehouse Module
            ['name' => 'warehouse.view', 'display_name' => 'View Warehouse', 'module' => 'warehouse'],
            ['name' => 'warehouse.receive', 'display_name' => 'Receive Goods', 'module' => 'warehouse'],

            // Kitchen Module
            ['name' => 'kitchen.view_schedule', 'display_name' => 'View Cooking Schedule', 'module' => 'kitchen'],
            ['name' => 'kitchen.update_status', 'display_name' => 'Update Cooking Status', 'module' => 'kitchen'],

            // Finance Module
            ['name' => 'finance.view', 'display_name' => 'View Finance', 'module' => 'finance'],
            ['name' => 'finance.process_payment', 'display_name' => 'Process Payment', 'module' => 'finance'],
            ['name' => 'finance.calculate_profit', 'display_name' => 'Calculate Profit', 'module' => 'finance'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'display_name' => $permission['display_name'],
                    'module' => $permission['module'],
                    'guard_name' => 'web',
                ]
            );
        }
    }
}
