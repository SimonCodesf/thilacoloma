#!/bin/bash

# Statamic Server Management Script

case "$1" in
    start)
        echo "Starting Statamic server..."
        cd "/Users/simon/Library/CloudStorage/OneDrive-Persoonlijk/Thila Coloma/Thilacoloma_statamic"
        nohup php artisan serve --host=0.0.0.0 --port=8000 > server.log 2>&1 &
        echo "Server started in background. Check server.log for output."
        echo "Server running at: http://localhost:8000"
        ;;
    stop)
        echo "Stopping Statamic server..."
        pkill -f "php artisan serve"
        echo "Server stopped."
        ;;
    status)
        if pgrep -f "php artisan serve" > /dev/null; then
            echo "Server is running at http://localhost:8000"
            echo "Process ID: $(pgrep -f 'php artisan serve')"
        else
            echo "Server is not running."
        fi
        ;;
    restart)
        echo "Restarting Statamic server..."
        pkill -f "php artisan serve"
        sleep 2
        cd "/Users/simon/Library/CloudStorage/OneDrive-Persoonlijk/Thila Coloma/Thilacoloma_statamic"
        nohup php artisan serve --host=0.0.0.0 --port=8000 > server.log 2>&1 &
        echo "Server restarted. Check server.log for output."
        echo "Server running at: http://localhost:8000"
        ;;
    log)
        echo "Showing server log (last 20 lines):"
        cd "/Users/simon/Library/CloudStorage/OneDrive-Persoonlijk/Thila Coloma/Thilacoloma_statamic"
        tail -20 server.log
        ;;
    *)
        echo "Usage: $0 {start|stop|status|restart|log}"
        echo "Commands:"
        echo "  start   - Start the server in background"
        echo "  stop    - Stop the server"
        echo "  status  - Check if server is running"
        echo "  restart - Restart the server"
        echo "  log     - Show recent server log entries"
        exit 1
        ;;
esac
