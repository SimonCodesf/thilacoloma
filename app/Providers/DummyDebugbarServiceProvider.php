<?php

namespace Barryvdh\Debugbar;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Dummy ServiceProvider to prevent "Class not found" errors in production
 * This is a temporary fix for production environments where debugbar is not installed
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Do nothing in production
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Do nothing in production
    }
}
