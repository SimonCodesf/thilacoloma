#!/bin/bash

# Auto GitHub Sync Monitor
# Runs in background and automatically syncs when GitHub has new commits

SYNC_SCRIPT="$(dirname "$0")/sync-content.sh"
CHECK_INTERVAL=300  # Check every 5 minutes
LOG_FILE="$(dirname "$0")/storage/logs/auto-sync-monitor.log"

# Ensure log directory exists
mkdir -p "$(dirname "$LOG_FILE")"

log_message() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') [MONITOR] $1" | tee -a "$LOG_FILE"
}

log_message "🚀 Starting Auto GitHub Sync Monitor (checking every ${CHECK_INTERVAL}s)"

while true; do
    # Check if sync script exists
    if [ ! -f "$SYNC_SCRIPT" ]; then
        log_message "❌ Sync script not found: $SYNC_SCRIPT"
        sleep $CHECK_INTERVAL
        continue
    fi
    
    # Run auto-sync (this will check GitHub and sync if needed)
    log_message "🔍 Running auto-sync check..."
    
    if "$SYNC_SCRIPT" --auto >> "$LOG_FILE" 2>&1; then
        log_message "✅ Auto-sync check completed successfully"
    else
        log_message "⚠️ Auto-sync check had issues (check logs)"
    fi
    
    # Wait before next check
    sleep $CHECK_INTERVAL
done