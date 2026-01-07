<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        Schema::defaultStringLength(191);

        // Runtime Safety: Detect and fix stale Vimeo token
        $vimeoToken = config('vimeo.connections.main.access_token');
        if (!empty($vimeoToken) && strlen($vimeoToken) < 50) {
            // Token is likely stale (old 32-char token). 
            // Attempt to force-reload .env to override stale process environment
            try {
                if (file_exists(base_path('.env'))) {
                    $dotenv = \Dotenv\Dotenv::createMutable(base_path());
                    $dotenv->load();
                    
                    // Refresh config from new env values
                    config([
                        'vimeo.connections.main.access_token' => env('VIMEO_ACCESS'),
                        'vimeo.connections.main.client_id' => env('VIMEO_CLIENT'),
                        'vimeo.connections.main.client_secret' => env('VIMEO_SECRET'),
                    ]);
                    
                    \Illuminate\Support\Facades\Log::warning('AppServiceProvider: Stale Vimeo token detected and auto-corrected from .env');
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('AppServiceProvider: Failed to auto-correct stale Vimeo token: ' . $e->getMessage());
            }
        }
    }
}
