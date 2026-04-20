<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->handle(Request::capture());

use App\Models\PurchaseOrder;
use App\Services\InvoiceService;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

$poNumber = 'PO/2026/04/003';
$po = PurchaseOrder::where('po_number', $poNumber)->first();

if ($po) {
    app(InvoiceService::class)->generateFromPo($po);
    echo "Invoice generated successfully for $poNumber\n";
} else {
    echo "PO $poNumber not found\n";
}
