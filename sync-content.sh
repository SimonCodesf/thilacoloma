#!/bin/bash

# Content Sync Script for Thila Coloma Statamic
# Synchronizes content between local development and server
# Now includes automatic GitHub monitoring!

set -e  # Exit on any error

# Configuration
SERVER_USER="thilacom"
SERVER_HOST="103.76.86.167"
SERVER_PATH="/home/thilacom/public_html"
LOCAL_PATH="$(pwd)"
GITHUB_REPO="SimonCodesf/thilacoloma"
MAIN_BRANCH="master"

# Kleuren voor output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Logging functie
log() {
    echo -e "${BLUE}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1"
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

# Functie om te controleren of server bereikbaar is
check_server_connection() {
    log "Checking server connection..."
    if ! ssh -q "$SERVER" exit; then
        error "Cannot connect to server $SERVER"
    fi
    success "Server connection OK"
}

# Functie om backup te maken
create_backup() {
    local backup_dir="$LOCAL_PATH/backups/$(date '+%Y%m%d_%H%M%S')"
    log "Creating backup in $backup_dir"
    
    mkdir -p "$backup_dir"
    cp -r "$LOCAL_PATH/content" "$backup_dir/"
    
    success "Backup created: $backup_dir"
}

# Functie om server content te downloaden
sync_from_server() {
    log "Syncing content FROM server TO local..."
    
    # Content collections
    scp -r "$SERVER:$REMOTE_PATH/content/collections/" "$LOCAL_PATH/content/"
    
    # Globals
    scp -r "$SERVER:$REMOTE_PATH/content/globals/" "$LOCAL_PATH/content/"
    
    # Assets meta (if needed)
    if ssh "$SERVER" "[ -d '$REMOTE_PATH/content/assets' ]"; then
        scp -r "$SERVER:$REMOTE_PATH/content/assets/" "$LOCAL_PATH/content/" 2>/dev/null || true
    fi
    
    success "Content synced from server"
}

# Functie om lokale content naar server te uploaden
sync_to_server() {
    log "Syncing content FROM local TO server..."
    
    # Maak eerst backup op server
    ssh "$SERVER" "mkdir -p $REMOTE_PATH/backups/$(date '+%Y%m%d_%H%M%S') && cp -r $REMOTE_PATH/content $REMOTE_PATH/backups/$(date '+%Y%m%d_%H%M%S')/"
    
    # Upload content
    scp -r "$LOCAL_PATH/content/collections/" "$SERVER:$REMOTE_PATH/content/"
    scp -r "$LOCAL_PATH/content/globals/" "$SERVER:$REMOTE_PATH/content/"
    
    # Fix permissions
    ssh "$SERVER" "chown -R www-data:www-data $REMOTE_PATH/content && chmod -R 644 $REMOTE_PATH/content/**/*.md $REMOTE_PATH/content/**/*.yaml"
    
    # Clear caches
    ssh "$SERVER" "cd $REMOTE_PATH && php please cache:clear && php please view:clear"
    
    success "Content synced to server and caches cleared"
}

# Functie om verschillen te controleren
check_differences() {
    log "Checking for differences between local and server..."
    
    # Download server content to temp directory
    local temp_dir="/tmp/thilacoloma_server_$(date '+%s')"
    mkdir -p "$temp_dir"
    
    scp -r "$SERVER:$REMOTE_PATH/content/" "$temp_dir/"
    
    # Compare
    if diff -r "$LOCAL_PATH/content" "$temp_dir/content" > /dev/null; then
        success "No differences found - local and server are in sync"
        rm -rf "$temp_dir"
        return 0
    else
        warning "Differences found between local and server content"
        echo "Run with --diff to see detailed differences"
        rm -rf "$temp_dir"
        return 1
    fi
}

# Functie om gedetailleerde verschillen te tonen
show_differences() {
    log "Showing detailed differences..."
    
    local temp_dir="/tmp/thilacoloma_server_$(date '+%s')"
    mkdir -p "$temp_dir"
    
    scp -r "$SERVER:$REMOTE_PATH/content/" "$temp_dir/"
    
    echo -e "\n${YELLOW}=== DIFFERENCES ===>${NC}"
    diff -u "$LOCAL_PATH/content" "$temp_dir/content" || true
    echo -e "${YELLOW}<== END DIFFERENCES ===${NC}\n"
    
    rm -rf "$temp_dir"
}

# Help functie
show_help() {
    echo "Thila Coloma Content Sync Script"
    echo ""
    echo "Usage: $0 [OPTION]"
    echo ""
    echo "Options:"
    echo "  --pull, -p          Sync content from server to local (download)"
    echo "  --push, -u          Sync content from local to server (upload)"
    echo "  --check, -c         Check for differences without syncing"
    echo "  --diff, -d          Show detailed differences"
    echo "  --help, -h          Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0 --pull           # Download latest content from server"
    echo "  $0 --push           # Upload local changes to server"
    echo "  $0 --check          # Check if local and server are in sync"
    echo ""
}

# Check for new commits from GitHub
check_github_updates() {
    log "🔍 Checking GitHub for new commits..." "INFO"
    
    # Fetch latest from GitHub
    git fetch origin $MAIN_BRANCH --quiet
    
    # Check if we're behind
    LOCAL_COMMIT=$(git rev-parse HEAD)
    REMOTE_COMMIT=$(git rev-parse origin/$MAIN_BRANCH)
    
    if [ "$LOCAL_COMMIT" != "$REMOTE_COMMIT" ]; then
        # Count commits we're behind
        BEHIND_COUNT=$(git rev-list --count HEAD..origin/$MAIN_BRANCH)
        log "📥 Found $BEHIND_COUNT new commit(s) on GitHub" "WARNING"
        return 1  # We are behind
    else
        log "✅ Local is up to date with GitHub" "SUCCESS"
        return 0  # We are up to date
    fi
}

# Pull latest changes from GitHub
pull_from_github() {
    log "📥 Pulling latest changes from GitHub..." "INFO"
    
    # Create backup of current state
    create_backup "before-github-pull"
    
    # Pull changes
    git pull origin $MAIN_BRANCH
    
    log "✅ Successfully pulled from GitHub" "SUCCESS"
}

# Auto-sync: Check GitHub and sync if needed
auto_sync() {
    log "🔄 Starting auto-sync process..." "INFO"
    
    if ! check_github_updates; then
        log "🚀 Auto-syncing new changes from GitHub..." "INFO"
        pull_from_github
        
        # Now sync the new content to server
        log "📤 Syncing updated content to server..." "INFO"
        sync_to_server
        
        log "🎉 Auto-sync complete! Local → GitHub → Server" "SUCCESS"
    else
        log "😊 No auto-sync needed, everything is up to date" "SUCCESS"
    fi
}
        # Main execution
case "${1:-}" in
    "--pull")
        sync_from_server
        ;;
    "--push")
        sync_to_server
        ;;
    "--check")
        check_differences
        ;;
    "--diff")
        show_differences
        ;;
    "--github")
        pull_from_github
        ;;
    "--auto")
        auto_sync
        ;;
    *)
        echo "🔄 Thila Coloma Content Sync Tool"
        echo ""
        echo "Usage: $0 [OPTION]"
        echo ""
        echo "Options:"
        echo "  --pull     Pull content from server to local"
        echo "  --push     Push content from local to server"  
        echo "  --check    Check sync status between local and server"
        echo "  --diff     Show detailed differences"
        echo "  --github   Pull latest changes from GitHub"
        echo "  --auto     Auto-sync: GitHub → Local → Server"
        echo ""
        echo "Examples:"
        echo "  $0 --pull    # Download latest from server"
        echo "  $0 --push    # Upload local changes to server"
        echo "  $0 --auto    # Full auto-sync workflow"
        ;;
esac
        --push|-u)
            check_server_connection
            if ! check_differences; then
                warning "There are differences. Consider pulling first to avoid overwriting server changes."
                read -p "Continue with push? (y/N): " -n 1 -r
                echo
                if [[ ! $REPLY =~ ^[Yy]$ ]]; then
                    log "Push cancelled by user"
                    exit 0
                fi
            fi
            sync_to_server
            ;;
        --check|-c)
            check_server_connection
            check_differences
            ;;
        --diff|-d)
            check_server_connection
            show_differences
            ;;
        --help|-h)
            show_help
            ;;
        "")
            warning "No option provided. Use --help for usage information."
            show_help
            exit 1
            ;;
        *)
            error "Unknown option: $1. Use --help for usage information."
            ;;
    esac
}

# Run main function
main "$@"