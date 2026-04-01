<?php

namespace App\Providers;

use App\Models\Dapur;
use App\Models\Investor;
use App\Models\MenuBom;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Observers\DapurObserver;
use App\Observers\InvestorObserver;
use App\Observers\MenuBomObserver;
use App\Observers\PurchaseOrderObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-approvals', function (User $user) {
            return $user->hasRole(['admin', 'superadmin']);
        });

        // Register Observers for Wallet Auto-creation (Fase 1.2)
        Dapur::observe(DapurObserver::class);
        Investor::observe(InvestorObserver::class);

        // Register Observers for Nutrition Sync (Fase 2.2)
        MenuBom::observe(MenuBomObserver::class);

        // Register Observers for Purchase Order (Fase 4)
        PurchaseOrder::observe(PurchaseOrderObserver::class);
    }
}
