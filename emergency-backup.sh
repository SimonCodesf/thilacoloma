#!/bin/bash

# 🚨 Emergency Server Backup Script
# ALWAYS run this before any risky server operations

set -e

echo "🚨 EMERGENCY BACKUP STARTING..."

# Get current timestamp
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_NAME="emergency_backup_${TIMESTAMP}"

# Create backup on server
echo "📦 Creating server backup..."
ssh thilacoloma "cd /var/www && tar -czf ${BACKUP_NAME}.tar.gz thilacoloma/ && echo '✅ Backup created: ${BACKUP_NAME}.tar.gz'"

# Download backup locally as extra safety
echo "📥 Downloading backup locally..."
mkdir -p backups/emergency
scp thilacoloma:/var/www/${BACKUP_NAME}.tar.gz backups/emergency/

# Create local git backup
echo "🔄 Creating local git backup..."
git branch emergency-backup-${TIMESTAMP} 2>/dev/null || echo "Branch already exists"

# Log the backup
echo "📝 Logging backup..."
echo "$(date): Emergency backup created - ${BACKUP_NAME}" >> backups/backup_log.txt

echo ""
echo "✅ EMERGENCY BACKUP COMPLETE!"
echo "📁 Server backup: /var/www/${BACKUP_NAME}.tar.gz"
echo "📁 Local backup: backups/emergency/${BACKUP_NAME}.tar.gz" 
echo "🌿 Git branch: emergency-backup-${TIMESTAMP}"
echo ""
echo "⚠️  You can now proceed with your risky operation"
echo "🔄 To restore: ./emergency-restore.sh ${BACKUP_NAME}"