#!/bin/bash

# Fix permissions script for Statamic deployment
# This script ensures all files have proper ownership and permissions for web server

echo "🔧 Fixing file permissions and ownership..."

# Set proper ownership for the entire project
chown -R www-data:www-data /var/www/thilacoloma

# Set proper permissions for directories
find /var/www/thilacoloma -type d -exec chmod 775 {} \;

# Set proper permissions for files  
find /var/www/thilacoloma -type f -exec chmod 664 {} \;

# Set executable permissions for specific files
chmod 755 /var/www/thilacoloma/please
chmod 644 /var/www/thilacoloma/.env*

# Fix storage and cache directories specifically
chmod -R 775 /var/www/thilacoloma/storage
chmod -R 775 /var/www/thilacoloma/bootstrap/cache

# Remove any root-owned cache files and recreate structure
echo "🗑️  Cleaning up root-owned cache files..."
find /var/www/thilacoloma/storage/framework/cache/data/stache/ -user root -delete 2>/dev/null || true

# Ensure Stache cache directories exist with correct ownership
mkdir -p /var/www/thilacoloma/storage/framework/cache/data/stache/indexes/{collections,taxonomies,navs,globals,assets,forms,users}
chown -R www-data:www-data /var/www/thilacoloma/storage/framework/cache/data/stache/
chmod -R 775 /var/www/thilacoloma/storage/framework/cache/data/stache/

echo "✅ Permissions fixed successfully!"
echo "   - All files owned by www-data:www-data"
echo "   - Directories: 775 permissions"  
echo "   - Files: 664 permissions"
echo "   - Storage: 775 permissions"
echo "   - Cache: Cleaned and rebuilt"
