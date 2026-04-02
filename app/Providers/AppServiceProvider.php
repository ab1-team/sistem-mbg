<?php

namespace App\Providers;

use App\Http\Middleware\ScopeDapurBySubdomain;
use App\Models\Dapur;
use App\Models\Investor;
use App\Models\MenuBom;
use App\Models\User;
use App\Observers\DapurObserver;
use App\Observers\InvestorObserver;
use App\Observers\MenuBomObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

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

        // Rate Limiting (Fase 7.2)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Pastikan Livewire update request melewati middleware tenancy
        // agar database tenant aktif saat auth()->user() dipanggil Livewire.
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/livewire/update', $handle)->middleware([
                'web',
                InitializeTenancyByDomain::class,
                PreventAccessFromCentralDomains::class,
                ScopeDapurBySubdomain::class,
            ]);
        });
    }
}
