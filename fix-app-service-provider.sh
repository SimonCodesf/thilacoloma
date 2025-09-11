#!/bin/bash
set -e

echo "🔧 Fixing AppServiceProvider debugbar registration..."

# Deploy the fix
git add app/Providers/AppServiceProvider.php
git commit -m "Fix debugbar ServiceProvider class reference to use string instead of class constant"
git push origin copilot/vscode1756999013315

# Deploy to production
ssh -i ~/.ssh/thilacoloma-web-key simon@103.76.86.167 << 'REMOTE'
    cd /var/www/thilacoloma.be/public_html
    echo "📥 Pulling latest changes..."
    git pull origin copilot/vscode1756999013315
    
    echo "🔄 Clearing all caches..."
    php please cache:clear
    php please config:clear
    php please config:cache
    php please route:clear
    php please view:clear
    
    echo "🔧 Regenerating autoloader..."
    composer dump-autoload -o
    
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
    fi
}
