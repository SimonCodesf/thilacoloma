#!/bin/bash

# Configuration
GITHUB_REPO="https://github.com/preciousbetine/thilacolomaweb.git"
GITHUB_BRANCH="main"
LOCAL_REPO_DIR="/Users/simon/Library/CloudStorage/OneDrive-Persoonlijk/Thila Coloma/Thilacoloma_statamic"
SERVER_CONTENT_DIR="/home/thilacom/public_html/content"
SYNC_INTERVAL=30  # seconds between checks
LOG_FILE="$LOCAL_REPO_DIR/auto-sync.log"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Logging function
log() {
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    echo -e "${BLUE}[$timestamp]${NC} $1"
    echo "[$timestamp] $1" >> "$LOG_FILE"
}

warning() {
    echo -e "${YELLOW}⚠️  WARNING:${NC} $1"
}

error() {
    echo -e "${RED}❌ ERROR:${NC} $1"
    exit 1
}

success() {
    echo -e "${GREEN}✅ SUCCESS:${NC} $1"
}

# SSH connection function using SSH config
ssh_to_server() {
    local command="$1"
    ssh thilacoloma "$command" 2>/dev/null
}

# SCP function using SSH config
scp_to_server() {
    local source="$1"
    local destination="$2"
    scp -r "$source" "$destination" 2>/dev/null
}

# Function to check server connection
check_server_connection() {
    log "Checking server connection..."
    if ! ssh_to_server "exit" &>/dev/null; then
        error "Cannot connect to server thilacoloma"
    fi
    success "Server connection OK"
}

# Function to create backup
create_backup() {
    local backup_dir="$LOCAL_REPO_DIR/backups/$(date '+%Y%m%d_%H%M%S')"
    log "Creating backup in $backup_dir"
    
    mkdir -p "$backup_dir"
    cp -r "$LOCAL_REPO_DIR/content" "$backup_dir/"
    
    success "Backup created: $backup_dir"
}

# Function to sync content from server to local
sync_from_server() {
    log "Syncing content FROM server TO local..."
    
    # Content collections
    if scp_to_server "thilacoloma:$SERVER_CONTENT_DIR/collections/" "$LOCAL_REPO_DIR/content/"; then
        log "✓ Collections synced from server"
    else
        log "⚠️ Failed to sync collections from server"
    fi
    
    # Globals
    if scp_to_server "thilacoloma:$SERVER_CONTENT_DIR/globals/" "$LOCAL_REPO_DIR/content/"; then
        log "✓ Globals synced from server"
    else
        log "⚠️ Failed to sync globals from server"
    fi
    
    # Assets (optional, might be large)
    if ssh_to_server "[ -d '$SERVER_CONTENT_DIR/assets' ]"; then
        if scp_to_server "thilacoloma:$SERVER_CONTENT_DIR/assets/" "$LOCAL_REPO_DIR/content/" 2>/dev/null; then
            log "✓ Assets synced from server"
        fi
    fi
    
    success "Server to local sync complete"
}

# Function to sync content from local to server
sync_to_server() {
    log "Syncing content FROM local TO server..."
    
    # Create server backup first
    log "Creating server backup..."
    ssh_to_server "mkdir -p $SERVER_CONTENT_DIR/../backups/$(date '+%Y%m%d_%H%M%S') && cp -r $SERVER_CONTENT_DIR $SERVER_CONTENT_DIR/../backups/$(date '+%Y%m%d_%H%M%S')/"
    
    # Sync collections and globals
    if scp_to_server "$LOCAL_REPO_DIR/content/collections/" "thilacoloma:$SERVER_CONTENT_DIR/" &&
       scp_to_server "$LOCAL_REPO_DIR/content/globals/" "thilacoloma:$SERVER_CONTENT_DIR/"; then
        
        # Fix permissions and clear cache
        ssh_to_server "chown -R www-data:www-data $SERVER_CONTENT_DIR && chmod -R 644 $SERVER_CONTENT_DIR/**/*.md $SERVER_CONTENT_DIR/**/*.yaml"
        
        # Clear Statamic cache
        ssh_to_server "cd /home/thilacom/public_html && php please cache:clear && php please view:clear"
        
        success "Local to server sync complete"
        return 0
    else
        error "Failed to sync content to server"
        return 1
    fi
}

# Function to commit and push changes to GitHub
commit_and_push() {
    cd "$LOCAL_REPO_DIR" || error "Cannot change to local repo directory"
    
    # Check if there are changes
    if git diff --quiet && git diff --cached --quiet; then
        log "No changes to commit"
        return 0
    fi
    
    log "Committing and pushing changes to GitHub..."
    
    git add content/
    git commit -m "Auto-sync: $(date '+%Y-%m-%d %H:%M:%S')"
    
    if git push origin "$GITHUB_BRANCH"; then
        success "Changes pushed to GitHub"
        return 0
    else
        error "Failed to push changes to GitHub"
        return 1
    fi
}

# Function to pull changes from GitHub
pull_from_github() {
    cd "$LOCAL_REPO_DIR" || error "Cannot change to local repo directory"
    
    log "Pulling latest changes from GitHub..."
    
    if git pull origin "$GITHUB_BRANCH"; then
        log "✓ Pulled latest changes from GitHub"
        return 0
    else
        log "⚠️ Failed to pull from GitHub"
        return 1
    fi
}

# Function for continuous sync monitoring
auto_sync() {
    log "Starting auto-sync monitoring..."
    log "Monitoring GitHub every $SYNC_INTERVAL seconds"
    
    local last_commit=""
    
    while true; do
        # Check for GitHub changes
        cd "$LOCAL_REPO_DIR" || error "Cannot change to local repo directory"
        
        # Fetch latest commits
        git fetch origin "$GITHUB_BRANCH" &>/dev/null
        
        # Get latest remote commit hash
        local current_commit=$(git rev-parse "origin/$GITHUB_BRANCH")
        
        if [ "$current_commit" != "$last_commit" ] && [ -n "$last_commit" ]; then
            log "New changes detected on GitHub!"
            
            # Pull changes
            if pull_from_github; then
                # Sync to server
                if sync_to_server; then
                    log "✅ Complete sync cycle completed"
                else
                    log "❌ Server sync failed"
                fi
            fi
        fi
        
        last_commit="$current_commit"
        sleep "$SYNC_INTERVAL"
    done
}

# Main script logic
case "$1" in
    "check")
        check_server_connection
        ;;
    "backup")
        create_backup
        ;;
    "from-server")
        check_server_connection
        sync_from_server
        commit_and_push
        ;;
    "to-server")
        check_server_connection
        sync_to_server
        ;;
    "full-sync")
        check_server_connection
        create_backup
        pull_from_github
        sync_to_server
        ;;
    "monitor")
        check_server_connection
        auto_sync
        ;;
    *)
        echo "Usage: $0 {check|backup|from-server|to-server|full-sync|monitor}"
        echo ""
        echo "Commands:"
        echo "  check       - Test server connection"
        echo "  backup      - Create local backup"
        echo "  from-server - Sync content from server to local and push to GitHub"
        echo "  to-server   - Sync content from local to server"
        echo "  full-sync   - Pull from GitHub and sync to server"
        echo "  monitor     - Start continuous monitoring for GitHub changes"
        exit 1
        ;;
esac