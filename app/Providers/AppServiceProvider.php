<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Statamic\Statamic;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Only register Debugbar in development environments
        $debugbarClass = '\\Barryvdh\\Debugbar\\ServiceProvider';
        if ($this->app->environment('local', 'testing') && class_exists($debugbarClass)) {
            $this->app->register($debugbarClass);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Statamic::vite('app', [
        //     'resources/js/cp.js',
        //     'resources/css/cp.css',
        // ]);
    }
}
