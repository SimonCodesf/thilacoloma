#!/bin/bash

# Thila Coloma Deployment Script
echo "🚀 Starting deployment..."

# Pull latest changes from GitHub
echo "📥 Pulling latest changes..."
git pull origin master

# Install/update dependencies
echo "📦 Installing dependencies..."
# Configure composer timeout properly (not via --timeout flag which doesn't exist)
composer config process-timeout 600
composer config --no-plugins allow-plugins.php-http/discovery true
composer config --no-plugins allow-plugins.pixelfear/composer-dist-plugin true
composer config --no-plugins allow-plugins.pestphp/pest-plugin true
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress

# Generate application key if needed
if [ ! -f .env ]; then
    echo "🔑 Setting up environment..."
    cp .env.production .env
    php artisan key:generate
fi

# Cache configuration
echo "⚡ Optimizing performance..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (if any)
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Clear and warm up Statamic caches
echo "🧹 Clearing Statamic caches..."
php artisan statamic:stache:clear
php artisan statamic:stache:warm

# Set proper permissions
echo "🔒 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage/app storage/framework storage/logs

echo "✅ Deployment complete!"
