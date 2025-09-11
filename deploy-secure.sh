#!/bin/bash

# Secure deployment script using SSH keys
# This script uses SSH key authentication instead of passwords

set -e  # Exit on any error

echo "🚀 Starting secure deployment to Thilacoloma server..."

# Load environment variables if available
if [ -f .env.local ]; then
    source .env.local
    echo "✅ Environment variables loaded"
fi

# Use SSH key authentication (preferred)
echo "📡 Connecting via SSH key..."

# Deploy to server
ssh thilacoloma << 'REMOTE_COMMANDS'
    set -e
    
    echo "📥 Pulling latest changes..."
    cd /var/www/thilacoloma
    git pull origin copilot/vscode1756999013315
    
    echo "📦 Installing dependencies..."
    composer install --no-dev --optimize-autoloader
    
    echo "🔧 Fixing permissions..."
    ./fix-permissions.sh
    
    echo "🧹 Clearing caches..."
    php please cache:clear
    php please stache:clear
    
    echo "✅ Deployment complete!"
REMOTE_COMMANDS

echo "🎉 Secure deployment finished successfully!"
echo "🌐 Site available at: http://103.76.86.167"
echo "⚙️  Control Panel: http://103.76.86.167/cp"
