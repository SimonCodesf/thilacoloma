# Statamic VPS Deployment Guide

## 🚀 Quick Deployment to Reznor VPS

### Prerequisites
- Fresh Ubuntu 22.04 VPS
- Root access to the server
- Your GitHub repository is up-to-date

### Step 1: Upload Script to Server
```bash
# Copy the script to your server
scp deploy-statamic-vps.sh root@103.76.86.167:/root/

# Or create the file directly on the server and paste the contents
```

### Step 2: Run the Deployment Script
```bash
# SSH into your server
ssh root@103.76.86.167

# Make script executable (if uploaded via scp)
chmod +x deploy-statamic-vps.sh

# Run the deployment (basic setup)
./deploy-statamic-vps.sh

# OR with custom domain for SSL
DOMAIN_NAME=yourdomain.com ./deploy-statamic-vps.sh

# OR with custom GitHub repo
GITHUB_REPO_URL=https://github.com/YourUser/your-repo.git ./deploy-statamic-vps.sh
```

### Step 3: Post-Deployment Configuration
1. **Database Setup**: Update `/var/www/thilacoloma/.env` with your database credentials
2. **Test the Site**: Visit your server's IP address
3. **Access Control Panel**: Visit `http://your-ip/cp`

## 📋 What the Script Does

### System Setup
- ✅ Updates Ubuntu 22.04 packages
- ✅ Installs PHP 8.1 with all required extensions
- ✅ Installs and configures Nginx
- ✅ Installs Composer and Git
- ✅ Configures UFW firewall

### Project Setup
- ✅ Clones your GitHub repository
- ✅ Installs Composer dependencies
- ✅ Sets correct file permissions
- ✅ Creates .env file if needed
- ✅ Configures Nginx virtual host

### Optimization
- ✅ Optimizes PHP-FPM settings
- ✅ Enables Gzip compression
- ✅ Sets security headers
- ✅ Caches configurations
- ✅ Refreshes Statamic cache

### Security
- ✅ Configures firewall rules
- ✅ Sets proper file permissions
- ✅ Denies access to sensitive directories
- ✅ Optional SSL with Let's Encrypt

## 🔧 Useful Commands After Deployment

```bash
# Restart services
systemctl restart nginx
systemctl restart php8.1-fpm

# View logs
tail -f /var/log/nginx/error.log
tail -f /var/log/php8.1-fpm.log

# Update site from GitHub
cd /var/www/thilacoloma
git pull origin main
composer install --no-dev
php artisan config:cache
php please stache:refresh

# Clear caches
php artisan cache:clear
php artisan config:clear
php please stache:clear
```

## 🌍 Environment Variables

| Variable | Description | Example |
|----------|-------------|---------|
| `GITHUB_REPO_URL` | Your GitHub repository URL | `https://github.com/SimonCodesf/thilacoloma.git` |
| `DOMAIN_NAME` | Your domain name (for SSL) | `thilacoloma.com` |

## 🔒 SSL Setup

If you have a domain name pointing to your server:
```bash
DOMAIN_NAME=yourdomain.com ./deploy-statamic-vps.sh
```

This will automatically:
- Install Let's Encrypt SSL certificate
- Configure HTTPS redirect
- Setup auto-renewal

## 🆘 Troubleshooting

### Site shows Nginx default page
- Check if the site is enabled: `ls -la /etc/nginx/sites-enabled/`
- Restart Nginx: `systemctl restart nginx`

### PHP errors
- Check PHP-FPM status: `systemctl status php8.1-fpm`
- Check PHP error logs: `tail -f /var/log/php8.1-fpm.log`

### Permission issues
- Re-run permissions: `chown -R www-data:www-data /var/www/thilacoloma`
- Set storage permissions: `chmod -R 775 /var/www/thilacoloma/storage`

### Database connection errors
- Update `.env` with correct database credentials
- Test database connection: `mysql -u username -p database_name`

## 📞 Support

If you encounter issues:
1. Check the logs: `/var/log/nginx/error.log` and `/var/log/php8.1-fpm.log`
2. Verify services are running: `systemctl status nginx php8.1-fpm`
3. Check file permissions in `/var/www/thilacoloma`
