#!/bin/bash

# 🔧 Auto-Fix Permissions Script for Thila Coloma
# Fixes common Laravel/Statamic permission issues

WEBSITE_DIR="/var/www/thilacoloma"
LOG_FILE="$WEBSITE_DIR/permission-fixes.log"

echo "🔧 [$(date)] Starting permission fix..." | tee -a $LOG_FILE

# Check if running as root/sudo
if [[ $EUID -ne 0 ]]; then
    echo "⚠️  Warning: Not running as root. Trying with sudo..." | tee -a $LOG_FILE
    sudo "$0" "$@"
    exit $?
fi

# Fix ownership
echo "👥 Fixing ownership..." | tee -a $LOG_FILE
chown -R www-data:www-data $WEBSITE_DIR/storage/ $WEBSITE_DIR/bootstrap/cache/ 2>/dev/null

# Fix directory permissions (775)
echo "📁 Fixing directory permissions..." | tee -a $LOG_FILE
find $WEBSITE_DIR/storage -type d -exec chmod 775 {} \; 2>/dev/null
find $WEBSITE_DIR/bootstrap/cache -type d -exec chmod 775 {} \; 2>/dev/null

# Fix file permissions (664)  
echo "📄 Fixing file permissions..." | tee -a $LOG_FILE
find $WEBSITE_DIR/storage -type f -exec chmod 664 {} \; 2>/dev/null
find $WEBSITE_DIR/bootstrap/cache -type f -exec chmod 664 {} \; 2>/dev/null

# Clear caches to rebuild with correct permissions
echo "🧹 Clearing caches..." | tee -a $LOG_FILE
cd $WEBSITE_DIR
php artisan cache:clear >/dev/null 2>&1
php artisan config:clear >/dev/null 2>&1
php artisan statamic:stache:clear >/dev/null 2>&1

echo "✅ [$(date)] Permission fix complete!" | tee -a $LOG_FILE
