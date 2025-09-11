#!/bin/bash

echo "🆘 ULTIMATE DEBUGBAR FIX - Creating missing ServiceProvider directly"

cd /var/www/thilacoloma

# Create the exact missing class that Laravel is looking for
echo "📁 Creating Barryvdh/Debugbar directory structure..."
mkdir -p vendor/barryvdh/laravel-debugbar/src

# Create the ServiceProvider class file that Laravel expects
echo "📝 Creating ServiceProvider.php..."
cat > vendor/barryvdh/laravel-debugbar/src/ServiceProvider.php << 'EOFPHP'
<?php

namespace Barryvdh\Debugbar;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Emergency ServiceProvider to prevent "Class not found" errors
 * This is a dummy provider that does nothing in production
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Do nothing in production - this prevents the error
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Do nothing in production - this prevents the error
    }
}
EOFPHP

# Create a minimal composer.json for the dummy package
echo "📦 Creating dummy package composer.json..."
cat > vendor/barryvdh/laravel-debugbar/composer.json << 'EOFJSON'
{
    "name": "barryvdh/laravel-debugbar",
    "description": "Emergency dummy package to prevent ServiceProvider not found errors",
    "type": "library",
    "autoload": {
        "psr-4": {
            "Barryvdh\\Debugbar\\": "src/"
        }
    }
}
EOFJSON

# Regenerate autoloader to include our emergency fix
echo "🔄 Regenerating autoloader with emergency fix..."
composer dump-autoload --optimize --no-dev

# Clear all caches one more time
echo "🧹 Final cache clearing..."
php artisan config:clear
php artisan cache:clear
php artisan config:cache

echo "🆘 ULTIMATE FIX COMPLETE!"
echo "🌐 The ServiceProvider should now exist and prevent the error"
echo "🔧 Try the updater now: http://103.76.86.167/cp/updater/statamic"
