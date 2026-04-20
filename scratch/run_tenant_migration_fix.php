<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Stancl\Tenancy\Database\Models\Tenant;

Tenant::all()->each(function ($tenant) {
    echo 'Migrating tenant: '.$tenant->id."\n";
    $tenant->run(function () {
        Artisan::call('migrate', [
            '--path' => 'database/migrations/tenant/2026_04_20_030600_add_fulfillment_columns_to_po_tables_tenant.php',
            '--force' => true,
        ]);
        echo Artisan::output();
    });
});
