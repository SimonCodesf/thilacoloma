#!/bin/bash

# 🛡️ Safe Deployment Script
# Replaces dangerous git reset --hard with safe alternatives

set -e

echo "🚀 SAFE DEPLOYMENT STARTING..."

# Step 1: Emergency backup first
echo "1️⃣ Creating emergency backup..."
./emergency-backup.sh

echo ""
echo "2️⃣ Checking server status..."
ssh thilacoloma "cd /var/www/thilacoloma && git status --porcelain" > server_status.tmp

if [[ -s server_status.tmp ]]; then
    echo "⚠️  Server has uncommitted changes:"
    cat server_status.tmp
    echo ""
    echo "🔄 Stashing server changes..."
    ssh thilacoloma "cd /var/www/thilacoloma && git stash push -m 'Pre-deployment stash $(date)'"
    echo "✅ Changes stashed safely"
else
    echo "✅ Server is clean"
fi

echo ""
echo "3️⃣ Fetching latest changes..."
ssh thilacoloma "cd /var/www/thilacoloma && git fetch origin"

echo ""
echo "4️⃣ Checking for merge conflicts..."
MERGE_RESULT=$(ssh thilacoloma "cd /var/www/thilacoloma && git merge origin/master 2>&1" || echo "MERGE_FAILED")

if [[ "$MERGE_RESULT" == *"MERGE_FAILED"* ]] || [[ "$MERGE_RESULT" == *"CONFLICT"* ]]; then
    echo "❌ MERGE CONFLICTS DETECTED!"
    echo "🛑 STOPPING DEPLOYMENT FOR SAFETY"
    echo ""
    echo "📋 Manual steps required:"
    echo "1. ssh thilacoloma 'cd /var/www/thilacoloma && git status'"
    echo "2. Resolve conflicts manually"
    echo "3. ssh thilacoloma 'cd /var/www/thilacoloma && git add . && git commit'"
    echo "4. Re-run this script"
    echo ""
    echo "🔄 To restore from backup: ./emergency-restore.sh [backup-name]"
    exit 1
fi

echo "✅ Merge successful!"

echo ""
echo "5️⃣ Updating dependencies and cache..."
ssh thilacoloma "cd /var/www/thilacoloma && COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader"
ssh thilacoloma "cd /var/www/thilacoloma && php artisan config:clear && php artisan cache:clear && php artisan view:clear"
ssh thilacoloma "cd /var/www/thilacoloma && php artisan statamic:stache:clear && php artisan statamic:stache:warm"

echo ""
echo "🎉 SAFE DEPLOYMENT COMPLETE!"
echo "✅ No data was lost during this deployment"
echo "📁 Backup available if rollback needed"

# Clean up temp files
rm -f server_status.tmp