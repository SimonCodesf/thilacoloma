#!/bin/bash

echo "🔧 COMPOSER DEBUGBAR REMOVAL"

cd /var/www/thilacoloma

echo "📋 Checking current installed packages..."
composer show | grep debugbar || echo "Debugbar not found in composer show"

echo "🗑️ Attempting to remove debugbar completely..."
composer remove barryvdh/laravel-debugbar --no-interaction || echo "Package not installed, continuing..."

echo "🧹 Clearing composer cache..."
composer clear-cache

echo "🔄 Fresh install without debugbar..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "🔍 Verifying debugbar is gone..."
composer show | grep debugbar || echo "✅ Debugbar successfully removed"

echo "🧹 Final Laravel clearing..."
php artisan config:clear
php artisan cache:clear
php artisan config:cache

echo "✅ COMPOSER REMOVAL COMPLETE!"
