#!/bin/bash

#################################################################
# Statamic 4 VPS Deployment Script for Ubuntu 22.04
# Author: DevOps Expert
# Description: Complete setup script for fresh Ubuntu VPS
#################################################################

set -euo pipefail  # Exit on error, undefined vars, pipe failures

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration - Set these environment variables before running
GITHUB_REPO_URL="${GITHUB_REPO_URL:-https://github.com/SimonCodesf/thilacoloma.git}"
DOMAIN_NAME="${DOMAIN_NAME:-}" # Optional: set for SSL setup
PROJECT_NAME="thilacoloma"
WEB_ROOT="/var/www/$PROJECT_NAME"
NGINX_SITES_AVAILABLE="/etc/nginx/sites-available"
NGINX_SITES_ENABLED="/etc/nginx/sites-enabled"

# Logging function
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
check_root() {
    if [[ $EUID -ne 0 ]]; then
        log_error "This script must be run as root (use sudo)"
        exit 1
    fi
}

# Update system packages
update_system() {
    log "Updating system packages..."
    apt update -y
    apt upgrade -y
    log_success "System updated successfully"
}

# Install required software
install_software() {
    log "Installing required software for Statamic 4..."
    
    # Install PHP 8.1 and extensions
    apt install -y software-properties-common
    add-apt-repository ppa:ondrej/php -y
    apt update -y
    
    apt install -y \
        php8.3 \
        php8.3-cli \
        php8.3-fpm \
        php8.3-common \
        php8.3-mysql \
        php8.3-mbstring \
        php8.3-xml \
        php8.3-curl \
        php8.3-gd \
        php8.3-zip \
        php8.3-bcmath \
        php8.3-intl \
        php8.3-imagick \
        php8.3-redis \
        php8.3-sqlite3
    
    # Install Nginx
    apt install -y nginx
    
    # Install Git
    apt install -y git
    
    # Install unzip and curl
    apt install -y unzip curl
    
    # Install Composer
    if ! command -v composer &> /dev/null; then
        log "Installing Composer..."
        curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
        log_success "Composer installed successfully"
    else
        log "Composer already installed, updating..."
        composer self-update
    fi
    
    log_success "All required software installed"
}

# Configure UFW firewall
configure_firewall() {
    log "Configuring UFW firewall..."
    
    # Install UFW if not present
    apt install -y ufw
    
    # Reset to defaults
    ufw --force reset
    
    # Default policies
    ufw default deny incoming
    ufw default allow outgoing
    
    # Allow SSH (port 22)
    ufw allow ssh
    
    # Allow HTTP and HTTPS
    ufw allow 'Nginx Full'
    
    # Enable firewall
    ufw --force enable
    
    log_success "Firewall configured successfully"
}

# Clone or update project from GitHub
setup_project() {
    log "Setting up Statamic project..."
    
    # Create web root directory if it doesn't exist
    mkdir -p /var/www
    
    if [[ -d "$WEB_ROOT" ]]; then
        log "Project directory exists, pulling latest changes..."
        cd "$WEB_ROOT"
        git pull origin main 2>/dev/null || git pull origin master || {
            log_warning "Could not pull from git. Continuing with existing code..."
        }
    else
        log "Cloning project from GitHub..."
        git clone "$GITHUB_REPO_URL" "$WEB_ROOT"
        cd "$WEB_ROOT"
    fi
    
    # Install Composer dependencies
    log "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
    
    # Create .env file if it doesn't exist
    if [[ ! -f ".env" ]]; then
        log "Creating .env file from .env.example..."
        cp .env.example .env
        
        # Generate application key
        php artisan key:generate --force
        
        log_warning "Please update the .env file with your database credentials and other settings"
    fi
    
    log_success "Project setup completed"
}

# Set correct file permissions
set_permissions() {
    log "Setting correct file permissions..."
    
    cd "$WEB_ROOT"
    
    # Set ownership to www-data
    chown -R www-data:www-data .
    
    # Set directory permissions
    find . -type d -exec chmod 755 {} \;
    find . -type f -exec chmod 644 {} \;
    
    # Set write permissions for Statamic directories
    chmod -R 775 storage/
    chmod -R 775 bootstrap/cache/
    
    # Statamic specific directories (if they exist)
    [[ -d "content" ]] && chmod -R 775 content/
    [[ -d "users" ]] && chmod -R 775 users/
    [[ -d "resources" ]] && chmod -R 775 resources/
    [[ -d "public" ]] && chmod -R 755 public/
    
    # Make artisan executable
    chmod +x artisan
    
    log_success "File permissions set correctly"
}

# Configure Nginx
configure_nginx() {
    log "Configuring Nginx..."
    
    # Create Nginx configuration
    cat > "$NGINX_SITES_AVAILABLE/$PROJECT_NAME" << EOF
server {
    listen 80;
    listen [::]:80;
    
    server_name ${DOMAIN_NAME:-_};
    root $WEB_ROOT/public;
    index index.php index.html;
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    
    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/javascript;
    
    # Handle PHP files
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    
    # PHP-FPM configuration
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
    
    # Deny access to sensitive files
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    # Deny access to specific directories
    location ~ ^/(storage|bootstrap|config|database|resources|routes|tests|vendor)/ {
        deny all;
    }
    
    # Cache static assets
    location ~* \.(jpg|jpeg|gif|png|css|js|ico|svg)$ {
        expires 1M;
        add_header Cache-Control "public, immutable";
    }
    
    # Security.txt
    location = /.well-known/security.txt {
        return 301 \$scheme://\$server_name/security.txt;
    }
}
EOF

    # Enable the site
    if [[ -f "$NGINX_SITES_ENABLED/default" ]]; then
        rm "$NGINX_SITES_ENABLED/default"
    fi
    
    ln -sf "$NGINX_SITES_AVAILABLE/$PROJECT_NAME" "$NGINX_SITES_ENABLED/"
    
    # Test Nginx configuration
    if nginx -t; then
        log_success "Nginx configuration is valid"
    else
        log_error "Nginx configuration test failed"
        exit 1
    fi
}

# Configure PHP-FPM
configure_php_fpm() {
    log "Configuring PHP-FPM..."
    
    # Update PHP-FPM pool configuration for better performance
    cat > /etc/php/8.3/fpm/pool.d/www.conf << 'EOF'
[www]
user = www-data
group = www-data
listen = /var/run/php/php8.3-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.process_idle_timeout = 10s
pm.max_requests = 500
php_admin_value[memory_limit] = 256M
php_admin_value[upload_max_filesize] = 50M
php_admin_value[post_max_size] = 50M
php_admin_value[max_execution_time] = 300
php_admin_value[max_input_time] = 300
EOF

    log_success "PHP-FPM configured"
}

# Start and enable services
start_services() {
    log "Starting and enabling services..."
    
    # Enable and start PHP-FPM
    systemctl enable php8.3-fpm
    systemctl restart php8.3-fpm
    
    # Enable and start Nginx
    systemctl enable nginx
    systemctl restart nginx
    
    # Check service status
    if systemctl is-active --quiet php8.3-fpm && systemctl is-active --quiet nginx; then
        log_success "All services started successfully"
    else
        log_error "Some services failed to start"
        systemctl status php8.3-fpm nginx
        exit 1
    fi
}

# Setup SSL with Let's Encrypt (optional)
setup_ssl() {
    if [[ -n "$DOMAIN_NAME" ]]; then
        log "Setting up SSL certificate for $DOMAIN_NAME..."
        
        # Install Certbot
        apt install -y certbot python3-certbot-nginx
        
        # Get SSL certificate
        certbot --nginx -d "$DOMAIN_NAME" --non-interactive --agree-tos --email "admin@$DOMAIN_NAME" --redirect
        
        # Setup auto-renewal
        (crontab -l 2>/dev/null; echo "0 12 * * * /usr/bin/certbot renew --quiet") | crontab -
        
        log_success "SSL certificate installed and auto-renewal configured"
    else
        log_warning "DOMAIN_NAME not set, skipping SSL setup"
    fi
}

# Clear caches and optimize
optimize_application() {
    log "Optimizing Statamic application..."
    
    cd "$WEB_ROOT"
    
    # Clear caches
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    
    # Cache configuration for production
    php artisan config:cache
    
    # Statamic specific optimizations
    php please stache:clear
    php please stache:refresh
    
    log_success "Application optimized"
}

# Display final instructions
display_instructions() {
    echo
    log_success "=== DEPLOYMENT COMPLETED SUCCESSFULLY ==="
    echo
    echo "🌐 Your Statamic site is now live!"
    echo
    echo "📋 ACCESS INFORMATION:"
    echo "   Frontend: http://${DOMAIN_NAME:-$(curl -s ifconfig.me)}"
    echo "   Control Panel: http://${DOMAIN_NAME:-$(curl -s ifconfig.me)}/cp"
    echo
    echo "📁 PROJECT LOCATION:"
    echo "   Web Root: $WEB_ROOT"
    echo "   Nginx Config: $NGINX_SITES_AVAILABLE/$PROJECT_NAME"
    echo
    echo "🔧 USEFUL COMMANDS:"
    echo "   Restart Nginx: systemctl restart nginx"
    echo "   Restart PHP-FPM: systemctl restart php8.3-fpm"
    echo "   View Nginx logs: tail -f /var/log/nginx/error.log"
    echo "   View PHP-FPM logs: tail -f /var/log/php8.3-fpm.log"
    echo
    echo "⚙️  NEXT STEPS:"
    echo "   1. Update $WEB_ROOT/.env with your database credentials"
    echo "   2. Import your database if needed"
    echo "   3. Access the Control Panel at /cp"
    echo "   4. Test your site functionality"
    echo
    if [[ -n "$DOMAIN_NAME" ]]; then
        echo "🔒 SSL certificate has been installed for $DOMAIN_NAME"
    else
        echo "💡 To enable SSL, set DOMAIN_NAME and run: DOMAIN_NAME=yourdomain.com $0"
    fi
    echo
}

# Main execution
main() {
    log "Starting Statamic VPS deployment..."
    
    check_root
    update_system
    install_software
    configure_firewall
    setup_project
    set_permissions
    configure_nginx
    configure_php_fpm
    start_services
    
    # Only setup SSL if domain is provided
    if [[ -n "$DOMAIN_NAME" ]]; then
        setup_ssl
    fi
    
    optimize_application
    display_instructions
    
    log_success "Deployment completed! 🚀"
}

# Run main function
main "$@"
