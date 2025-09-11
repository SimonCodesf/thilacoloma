#!/bin/bash
set -e

echo "🔧 Deploying AppServiceProvider fix using root access..."

# Deploy to production using root credentials
sshpass -p '57RtLMJQ8F0D' ssh -o StrictHostKeyChecking=no root@103.76.86.167 << 'REMOTE'
    cd /var/www/thilacoloma
    echo "📥 Pulling latest changes..."
    git pull origin copilot/vscode1756999013315
    
    echo "🔄 Clearing all caches..."
    COMPOSER_ALLOW_SUPERUSER=1 php please cache:clear
    COMPOSER_ALLOW_SUPERUSER=1 php please config:clear
    COMPOSER_ALLOW_SUPERUSER=1 php please config:cache
    COMPOSER_ALLOW_SUPERUSER=1 php please route:clear
    COMPOSER_ALLOW_SUPERUSER=1 php please view:clear
    
    echo "🔧 Regenerating autoloader..."
    COMPOSER_ALLOW_SUPERUSER=1 composer dump-autoload -o
    
    echo "✅ Fix deployed successfully!"
REMOTE

echo "🎉 AppServiceProvider fix deployment complete!"
echo "🔍 Testing /cp/updater/statamic access..."

# Test the updater endpoint
curl -s -o /dev/null -w "%{http_code}\n" "https://thilacoloma.be/cp/updater/statamic" | {
    read http_code
    if [ "$http_code" = "200" ]; then
        echo "✅ Updater is now accessible (HTTP $http_code)"
    else
        echo "❌ Updater still returning HTTP $http_code"
        echo "Let's check the error logs..."
        sshpass -p '57RtLMJQ8F0D' ssh -o StrictHostKeyChecking=no root@103.76.86.167 "tail -20 /var/www/thilacoloma/storage/logs/laravel.log"
    fi
}
