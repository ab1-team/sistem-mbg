<?php

declare(strict_types=1);

use App\Http\Controllers\DapurController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Finance\FinancialPeriodController;
use App\Http\Controllers\Finance\InvoiceController;
use App\Http\Controllers\Finance\KitchenInvoiceController;
use App\Http\Controllers\Finance\ReportController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\Kitchen\CookingController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\MenuPeriodController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\PoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Supplier\PoController as SupplierPoController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Warehouse\GrController;
use App\Http\Middleware\ScopeDapurBySubdomain;
use App\Livewire\Finance\Journal;
use App\Livewire\Finance\ProfitSharing;
use App\Livewire\Finance\Reporting;
use App\Livewire\Finance\WithdrawalManagement;
use App\Livewire\Investor\WithdrawalRequestForm;
use App\Livewire\InvestorDashboard;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    ScopeDapurBySubdomain::class,
])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Adaptive System Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');

        Route::middleware('role:admin_yayasan|superadmin')->group(function () {
            Route::resource('dapurs', DapurController::class);
            Route::resource('suppliers', SupplierController::class);
            Route::resource('users', UserController::class);
            Route::resource('periods', PeriodController::class);
        });

        Route::resource('investors', InvestorController::class);

        Route::get('materials/download-template', [MaterialController::class, 'downloadTemplate'])->name('materials.download-template');
        Route::post('materials/import', [MaterialController::class, 'import'])->name('materials.import');
        Route::resource('materials', MaterialController::class);

        Route::get('menu-items/download-template', [MenuItemController::class, 'downloadTemplate'])->name('menu-items.download-template');
        Route::post('menu-items/import', [MenuItemController::class, 'import'])->name('menu-items.import');
        Route::resource('menu-items', MenuItemController::class);

        // Menu Period Approval Flow
        Route::post('menu-periods/{menuPeriod}/submit', [MenuPeriodController::class, 'submit'])->name('menu-periods.submit');
        Route::post('menu-periods/{menuPeriod}/approve', [MenuPeriodController::class, 'approve'])->name('menu-periods.approve');
        Route::post('menu-periods/{menuPeriod}/reject', [MenuPeriodController::class, 'reject'])->name('menu-periods.reject');
        Route::resource('menu-periods', MenuPeriodController::class);

        // SCM - Purchase Orders
        Route::get('purchase-orders/download-template', [PoController::class, 'downloadTemplate'])->name('purchase-orders.download-template');
        Route::post('purchase-orders/{purchaseOrder}/import', [PoController::class, 'importItems'])->name('purchase-orders.import');
        Route::post('menu-periods/{menuPeriod}/generate-po', [PoController::class, 'generateFromMenu'])->name('menu-periods.generate-po');
        Route::post('purchase-orders/{purchaseOrder}/submit-to-supplier', [PoController::class, 'submitToSupplier'])->name('purchase-orders.submit-to-supplier');
        Route::post('purchase-orders/{purchaseOrder}/cancel', [PoController::class, 'cancel'])->name('purchase-orders.cancel');
        Route::resource('purchase-orders', PoController::class);

        // Warehouse / Goods Receipt
        Route::get('warehouse/gr', [GrController::class, 'index'])->name('gr.index');
        Route::get('warehouse/gr/create/{purchaseOrder}', [GrController::class, 'create'])->name('gr.create');
        Route::post('warehouse/gr/store/{purchaseOrder}', [GrController::class, 'store'])->name('gr.store');
        Route::get('warehouse/gr/{goodsReceipt}', [GrController::class, 'show'])->name('gr.show');

        // Finance Module
        Route::prefix('finance')->group(function () {
            Route::name('finance.')->group(function () {
                Route::get('journal', Journal::class)->name('journal.index');
                Route::get('periods', [FinancialPeriodController::class, 'index'])->name('periods.index');
                Route::get('profit-sharing', ProfitSharing::class)->name('profit-sharing.index');
                Route::get('withdrawals', WithdrawalManagement::class)->name('withdrawals.index');
                Route::get('reports', Reporting::class)->name('reports.index');
                Route::get('reports/preview', [ReportController::class, 'preview'])->name('reports.preview');

                // Invoices
                Route::prefix('invoices')->name('invoices.')->group(function () {
                    Route::get('/', [InvoiceController::class, 'index'])->name('index');
                    Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
                    Route::post('/{invoice}/verify', [InvoiceController::class, 'verify'])->name('verify');
                    Route::post('/{invoice}/pay', [InvoiceController::class, 'pay'])->name('pay');
                    Route::get('/{invoice}/preview', [InvoiceController::class, 'previewPdf'])->name('preview');
                });

                // Kitchen Invoices (Consolidated)
                Route::prefix('kitchen-invoices')->name('kitchen-invoices.')->group(function () {
                    Route::get('/', [KitchenInvoiceController::class, 'index'])->name('index');
                    Route::get('/{purchaseOrder}/download', [KitchenInvoiceController::class, 'downloadPdf'])->name('download');
                });
            });
        });

        // Kitchen Operations
        Route::get('kitchen/dashboard', [CookingController::class, 'index'])->name('kitchen.index');
        Route::post('kitchen/cooking/{schedule}/prepare', [CookingController::class, 'prepare'])->name('kitchen.prepare');
        Route::post('kitchen/cooking/{schedule}/start', [CookingController::class, 'start'])->name('kitchen.start');
        Route::post('kitchen/cooking/{schedule}/finish', [CookingController::class, 'finish'])->name('kitchen.finish');
        Route::post('kitchen/cooking/{schedule}/distribute', [CookingController::class, 'distribute'])->name('kitchen.distribute');
        Route::get('kitchen/inventory', [CookingController::class, 'inventory'])->name('kitchen.inventory');

        // Investor Portal Routes
        Route::middleware('role:investor')->prefix('investor')->name('investor.')->group(function () {
            Route::get('dashboard', InvestorDashboard::class)->name('dashboard');
            Route::get('withdrawals/create', WithdrawalRequestForm::class)->name('withdrawals.create');
        });

        // Supplier Portal Routes
        Route::middleware('role:supplier')->prefix('supplier')->name('supplier.')->group(function () {
            Route::get('purchase-orders', [SupplierPoController::class, 'index'])->name('purchase-orders.index');
            Route::get('purchase-orders/{purchaseOrder}', [SupplierPoController::class, 'show'])->name('purchase-orders.show');
            Route::post('purchase-orders/{assignment}/respond', [SupplierPoController::class, 'respond'])->name('purchase-orders.respond');
        });
    });

    require __DIR__.'/auth.php';
});
