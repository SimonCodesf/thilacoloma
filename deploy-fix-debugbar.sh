#!/bin/bash

echo "🚀 Starting Debugbar Fix Deployment..."

# 1. Navigate to your site directory
cd /var/www/thilacoloma

# 2. Pull the latest fixes
echo "📥 Pulling latest fixes from Git..."
git config --global --add safe.directory /var/www/thilacoloma
git pull origin copilot/vscode1756999013315

# 3. Clear all caches first
echo "🧹 Clearing existing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear  
php artisan view:clear

# 4. Remove any existing vendor directory and reinstall
echo "📦 Reinstalling dependencies..."
rm -rf vendor/
composer install --no-dev --optimize-autoloader --no-interaction

# 5. Make sure environment is set correctly
echo "⚙️ Setting production environment..."
echo "APP_DEBUG=false" >> .env
echo "APP_ENV=production" >> .env

# 6. Regenerate autoloader to apply dont-discover changes
echo "🔄 Regenerating autoloader..."
composer dump-autoload --optimize --no-dev

# 7. Rebuild all caches
echo "🏗️ Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan statamic:stache:clear
php artisan statamic:stache:warm

# 8. Set proper permissions
echo "🔐 Setting proper permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo "✅ Deployment complete! Debugbar error should be fixed."
echo "🌐 Your site should now be working at http://103.76.86.167"
echo "👤 Antilope should have expanded permissions in the Control Panel"
