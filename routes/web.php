<?php

use App\Http\Controllers\DapurController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Finance\InvoiceController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\Kitchen\CookingController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\MenuPeriodController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\PoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Supplier\PoController as SupplierPoController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Warehouse\GrController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('dapurs', DapurController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('investors', InvestorController::class);
    Route::resource('periods', PeriodController::class);
    Route::resource('users', UserController::class);

    Route::get('materials/download-template', [MaterialController::class, 'downloadTemplate'])->name('materials.download-template');
    Route::post('materials/import', [MaterialController::class, 'import'])->name('materials.import');
    Route::resource('materials', MaterialController::class);
    Route::resource('menu-items', MenuItemController::class);

    // Menu Period Approval Flow (Fase 2.4)
    Route::post('menu-periods/{menuPeriod}/submit', [MenuPeriodController::class, 'submit'])->name('menu-periods.submit');
    Route::post('menu-periods/{menuPeriod}/approve', [MenuPeriodController::class, 'approve'])->name('menu-periods.approve');
    Route::post('menu-periods/{menuPeriod}/reject', [MenuPeriodController::class, 'reject'])->name('menu-periods.reject');
    Route::resource('menu-periods', MenuPeriodController::class);

    // SCM - Purchase Orders
    Route::post('menu-periods/{menuPeriod}/generate-po', [PoController::class, 'generateFromMenu'])->name('menu-periods.generate-po');
    Route::post('purchase-orders/{purchaseOrder}/submit-to-supplier', [PoController::class, 'submitToSupplier'])->name('purchase-orders.submit-to-supplier');
    Route::post('purchase-orders/{purchaseOrder}/cancel', [PoController::class, 'cancel'])->name('purchase-orders.cancel');
    Route::resource('purchase-orders', PoController::class);

    // Warehouse / Goods Receipt (Fase 4.1)
    Route::get('warehouse/gr', [GrController::class, 'index'])->name('gr.index');
    Route::get('warehouse/gr/create/{purchaseOrder}', [GrController::class, 'create'])->name('gr.create');
    Route::post('warehouse/gr/store/{purchaseOrder}', [GrController::class, 'store'])->name('gr.store');
    Route::get('warehouse/gr/{goodsReceipt}', [GrController::class, 'show'])->name('gr.show');

    // Finance / Invoices (Fase 4.2 & 4.3)
    Route::get('finance/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('finance/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('finance/invoices/{invoice}/verify', [InvoiceController::class, 'verify'])->name('invoices.verify');
    Route::post('finance/invoices/{invoice}/pay', [InvoiceController::class, 'pay'])->name('invoices.pay');
    Route::get('finance/invoices/{invoice}/download', [InvoiceController::class, 'downloadPdf'])->name('invoices.download');

    // Kitchen Operations (Fase 5.1 & 5.2)
    Route::get('kitchen/dashboard', [CookingController::class, 'index'])->name('kitchen.index');
    Route::post('kitchen/cooking/{schedule}/start', [CookingController::class, 'start'])->name('kitchen.start');
    Route::post('kitchen/cooking/{schedule}/finish', [CookingController::class, 'finish'])->name('kitchen.finish');
    Route::get('kitchen/inventory', [CookingController::class, 'inventory'])->name('kitchen.inventory');

    // Supplier Portal Routes
    Route::middleware('role:supplier')->prefix('supplier')->name('supplier.')->group(function () {
        Route::get('purchase-orders', [SupplierPoController::class, 'index'])->name('purchase-orders.index');
        Route::get('purchase-orders/{purchaseOrder}', [SupplierPoController::class, 'show'])->name('purchase-orders.show');
        Route::post('purchase-orders/{assignment}/respond', [SupplierPoController::class, 'respond'])->name('purchase-orders.respond');
    });
});

require __DIR__.'/auth.php';
