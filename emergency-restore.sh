#!/bin/bash

# 🔄 Emergency Restore Script
# Restore from emergency backup when things go wrong

set -e

if [[ -z "$1" ]]; then
    echo "❌ Usage: $0 <backup-name>"
    echo ""
    echo "📁 Available backups:"
    ls -la backups/emergency/ 2>/dev/null || echo "No emergency backups found"
    echo ""
    echo "🌿 Available git branches:"
    git branch | grep emergency-backup || echo "No emergency git backups found"
    exit 1
fi

BACKUP_NAME="$1"

echo "🚨 EMERGENCY RESTORE STARTING..."
echo "📦 Restoring from: ${BACKUP_NAME}"

# Confirm this is what they want
echo ""
echo "⚠️  This will OVERWRITE the current server state!"
echo "❓ Are you sure you want to restore from backup?"
read -p "Type 'YES' to continue: " confirmation

if [[ "$confirmation" != "YES" ]]; then
    echo "❌ Restore cancelled"
    exit 1
fi

echo ""
echo "1️⃣ Stopping server processes..."
# Add any server stop commands here if needed

echo ""
echo "2️⃣ Restoring server files..."
ssh thilacoloma "cd /var/www && rm -rf thilacoloma_restore_temp || true"
scp backups/emergency/${BACKUP_NAME}.tar.gz thilacoloma:/var/www/
ssh thilacoloma "cd /var/www && tar -xzf ${BACKUP_NAME}.tar.gz && mv thilacoloma thilacoloma_current_broken && mv thilacoloma_backup thilacoloma"

echo ""
echo "3️⃣ Restarting services..."
ssh thilacoloma "cd /var/www/thilacoloma && php artisan config:clear && php artisan cache:clear"

echo ""
echo "4️⃣ Testing restore..."
HEALTH_CHECK=$(ssh thilacoloma "cd /var/www/thilacoloma && php artisan --version" || echo "FAILED")
if [[ "$HEALTH_CHECK" == *"FAILED"* ]]; then
    echo "❌ Restore failed - server not responding correctly"
    exit 1
fi

echo ""
echo "🎉 EMERGENCY RESTORE COMPLETE!"
echo "✅ Server restored from backup: ${BACKUP_NAME}"
echo "🗑️  Broken version moved to: thilacoloma_current_broken"
echo ""
echo "📋 Next steps:"
echo "1. Test the website to ensure everything works"
echo "2. If successful, remove broken backup: ssh thilacoloma 'rm -rf /var/www/thilacoloma_current_broken'"
echo "3. Commit any necessary fixes to prevent future issues"