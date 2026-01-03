<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        // if (env('APP_ENV') !== 'local' || request()->header('X-Forwarded-Proto') === 'https') {
        //     URL::forceScheme('https');
        // }

        // Use custom pagination view globally
        Paginator::defaultView('vendor.livewire.gxon');
        Paginator::defaultSimpleView('vendor.livewire.gxon');
    }
}
