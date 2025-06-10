<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

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
    public function boot()
    {
        if (app()->environment('production')) {
            $credentials = env('GOOGLE_DRIVE_CREDENTIALS_JSON');

            if ($credentials) {
                File::ensureDirectoryExists(storage_path('app/google'));
                File::put(storage_path('app/google/credentials.json'), $credentials);
            }
        }
    }
}
