#!/bin/bash

# Quick server management script using SSH keys
# Usage: ./manage-server.sh [command]

case $1 in
    "logs")
        echo "📋 Showing recent logs..."
        ssh thilacoloma 'cd /var/www/thilacoloma && tail -50 storage/logs/laravel.log'
        ;;
    "status")
        echo "📊 Server status..."
        ssh thilacoloma 'cd /var/www/thilacoloma && php please about && echo "✅ Statamic is running"'
        ;;
    "fix")
        echo "🔧 Running permissions fix..."
        ssh thilacoloma 'cd /var/www/thilacoloma && ./fix-permissions.sh'
        ;;
    "cache")
        echo "🧹 Clearing caches..."
        ssh thilacoloma 'cd /var/www/thilacoloma && php please cache:clear && php please stache:clear'
        ;;
    "connect")
        echo "🔗 Connecting to server..."
        ssh thilacoloma
        ;;
    "deploy")
        echo "🚀 Running secure deployment..."
        ./deploy-secure.sh
        ;;
    *)
        echo "🛠️  Thilacoloma Server Management"
        echo ""
        echo "Usage: $0 [command]"
        echo ""
        echo "Commands:"
        echo "  logs     - Show recent Laravel logs"
        echo "  status   - Check server and Statamic status"  
        echo "  fix      - Run permissions fix script"
        echo "  cache    - Clear all caches"
        echo "  connect  - SSH into the server"
        echo "  deploy   - Run secure deployment"
        echo ""
        echo "Example: $0 status"
        ;;
esac
