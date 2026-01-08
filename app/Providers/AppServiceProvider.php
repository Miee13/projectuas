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
         * saat aplikasi berjalan di lingkungan produksi (Railway).
         * Ini akan memperbaiki masalah CSS yang tidak muncul.
         */
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}