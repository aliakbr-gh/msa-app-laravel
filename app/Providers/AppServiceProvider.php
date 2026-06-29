<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\LedgerModule;
use Illuminate\Support\Facades\Schema;

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
        Paginator::useBootstrapFive();

        View::composer('layout.layout', function ($view) {
            $modules = Schema::hasTable('ledger_modules')
                ? LedgerModule::where('is_active', true)->orderBy('title')->get()
                : collect();

            $view->with('ledgerModules', $modules);
        });
    }
}
