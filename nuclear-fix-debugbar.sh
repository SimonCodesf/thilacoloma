#!/bin/bash

echo "☢️  NUCLEAR DEBUGBAR FIX - COMPLETE RESET"

cd /var/www/thilacoloma

# STEP 1: Complete cache nuclear option
echo "🧨 Nuclear cache clearing..."
rm -rf storage/framework/cache/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*
rm -rf storage/logs/*
rm -rf bootstrap/cache/*
rm -rf storage/statamic/*

# STEP 2: Remove all compiled files
echo "💥 Removing all compiled files..."
rm -f storage/framework/cache/providers.php
rm -f storage/framework/cache/services.php
rm -f storage/framework/cache/packages.php
rm -f bootstrap/cache/services.php
rm -f bootstrap/cache/packages.php
rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/routes-v7.php

# STEP 3: Force environment
echo "⚙️ Force setting environment..."
cat > .env << 'EOL'
APP_NAME="Thila Coloma"
APP_ENV=production
APP_KEY=base64:YourAppKeyHere
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=http://103.76.86.167

DB_CONNECTION=sqlite
DB_DATABASE=database.sqlite

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

STATAMIC_ANTLERS_DEBUGBAR=false
DEBUGBAR_ENABLED=false
EOL

# STEP 4: Complete vendor rebuild
echo "🔄 Complete vendor rebuild..."
rm -rf vendor/
rm -f composer.lock

# STEP 5: Install with explicit exclusions
echo "📦 Installing packages..."
composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# STEP 6: Manually remove any debugbar references
echo "🗑️ Manually removing debugbar references..."
find vendor/ -name "*debugbar*" -type d -exec rm -rf {} + 2>/dev/null || true
find vendor/ -name "*Debugbar*" -type d -exec rm -rf {} + 2>/dev/null || true

# STEP 7: Generate fresh autoloader
echo "🔄 Regenerating autoloader..."
composer dump-autoload --optimize --no-dev --classmap-authoritative

# STEP 8: Clear Laravel again
echo "🧹 Final Laravel clearing..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# STEP 9: Rebuild caches without debugbar
echo "🏗️ Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# STEP 10: Statamic specific
echo "📊 Statamic clearing..."
php artisan statamic:stache:clear
php artisan statamic:stache:warm

# STEP 11: Permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 644 .env

echo "☢️  NUCLEAR FIX COMPLETE!"
echo "🌐 Test your site: http://103.76.86.167"
echo "🎛️  Test Control Panel: http://103.76.86.167/cp"
echo "🔧 Test Updater: http://103.76.86.167/cp/updater/statamic"
