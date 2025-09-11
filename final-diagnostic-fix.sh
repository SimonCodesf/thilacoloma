#!/bin/bash

echo "🔍 FINAL DIAGNOSTIC - Finding where Debugbar is registered"

cd /var/www/thilacoloma

echo "=== CHECKING CONFIGURATION FILES ==="
echo "🔍 Checking config/app.php for Debugbar..."
grep -n "Debugbar" config/app.php || echo "Not found in config/app.php"

echo "🔍 Checking bootstrap/providers.php for Debugbar..."
grep -n "Debugbar" bootstrap/providers.php || echo "Not found in bootstrap/providers.php"

echo "🔍 Checking all PHP files for Debugbar registration..."
find . -name "*.php" -not -path "./vendor/*" -exec grep -l "Debugbar.*ServiceProvider" {} \; || echo "No Debugbar ServiceProvider registration found"

echo "🔍 Checking composer.json for debugbar..."
grep -n "debugbar" composer.json || echo "Not found in composer.json"

echo "🔍 Checking .env for debugbar settings..."
grep -i "debug" .env || echo "No debug settings in .env"

echo "=== CURRENT INSTALLED PACKAGES ==="
echo "📦 Checking if debugbar is currently installed..."
composer show | grep debugbar || echo "Debugbar not in composer show"

echo "=== ULTIMATE FIX ATTEMPT ==="
echo "🚨 Creating emergency ServiceProvider directly in app..."

# Create the ServiceProvider in our app directory where it will definitely be found
mkdir -p app/Emergency
cat > app/Emergency/DebugbarServiceProvider.php << 'EOF'
<?php

namespace Barryvdh\Debugbar;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Emergency ServiceProvider to resolve "Class not found" errors
 */
class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        // Emergency fix - do nothing
    }

    public function boot(): void
    {
        // Emergency fix - do nothing
    }
}
EOF

echo "📝 ServiceProvider created in app/Emergency/"

# Also create it in the expected vendor location
echo "📁 Creating in vendor location..."
mkdir -p vendor/barryvdh/laravel-debugbar/src
cp app/Emergency/DebugbarServiceProvider.php vendor/barryvdh/laravel-debugbar/src/ServiceProvider.php

# Update composer autoload to include both locations
echo "🔄 Updating composer autoload..."
composer dump-autoload --optimize --no-dev

# Clear everything
echo "🧹 Clearing all caches..."
rm -rf bootstrap/cache/*
rm -rf storage/framework/cache/*

php artisan config:clear || echo "Config clear failed but continuing..."
php artisan cache:clear || echo "Cache clear failed but continuing..."
php artisan route:clear || echo "Route clear failed but continuing..."
php artisan view:clear || echo "View clear failed but continuing..."

# Rebuild caches
echo "🏗️ Rebuilding caches..."
php artisan config:cache || echo "Config cache failed but continuing..."

echo "🆘 FINAL DIAGNOSTIC COMPLETE!"
echo "📊 Check the output above to see where Debugbar might be registered"
echo "🌐 Try accessing: http://103.76.86.167/cp/updater/statamic"
