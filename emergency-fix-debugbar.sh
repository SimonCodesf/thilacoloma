#!/bin/bash

echo "🚨 EMERGENCY DEBUGBAR FIX - Starting..."

# Navigate to site directory
cd /var/www/thilacoloma

# AGGRESSIVE CACHE CLEARING
echo "🧹 Aggressive cache clearing..."
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/views/*
rm -rf storage/framework/sessions/*
rm -rf bootstrap/cache/*
rm -rf storage/logs/*

# Clear Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Remove service provider cache specifically
rm -rf storage/framework/cache/providers.php

# Pull latest fixes
echo "📥 Pulling latest fixes..."
git config --global --add safe.directory /var/www/thilacoloma
git pull origin copilot/vscode1756999013315

# COMPLETE VENDOR REBUILD
echo "📦 Complete vendor rebuild..."
rm -rf vendor/
rm -rf composer.lock

# Reinstall with specific composer settings
composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Force environment settings
echo "⚙️ Setting environment..."
sed -i '/APP_DEBUG=/d' .env
sed -i '/APP_ENV=/d' .env
echo "APP_DEBUG=false" >> .env
echo "APP_ENV=production" >> .env

# Regenerate everything
echo "🔄 Regenerating all autoloaders and caches..."
composer dump-autoload --optimize --no-dev
php artisan clear-compiled
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Statamic specific clearing
php artisan statamic:stache:clear
php artisan statamic:stache:warm

# Set permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo "✅ EMERGENCY FIX COMPLETE!"
echo "🌐 Try accessing your site now: http://103.76.86.167"
echo "🔍 Check Control Panel: http://103.76.86.167/cp"
