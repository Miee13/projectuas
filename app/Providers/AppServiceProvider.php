<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        /**
         * Memaksa penggunaan HTTPS untuk semua aset (CSS/JS) dan link
         */
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}