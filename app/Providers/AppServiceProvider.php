<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Always use APP_URL as the base URL for all generated URLs
        if (config('app.url')) {
            URL::forceRootUrl(config('app.url'));
        }
        
        // If APP_URL is HTTPS, force HTTPS scheme
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }

    public function register(): void
    {
        //
    }
}