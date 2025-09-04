# cPanel Deployment Configuration

This `.cpanel.yml` file enables automatic deployment through cPanel's Git™ Version Control.

## What it does:
1. Copies all files from the Git repository to your public web directory
2. Copies `.env.production` to `.env` for production settings
3. Configures Composer platform settings for PHP compatibility
4. Installs composer dependencies (production optimized)
5. Clears and optimizes Laravel/Statamic caches
6. Sets proper file permissions

## PHP Version Compatibility:
This project uses Statamic 5.0 which requires PHP 8.3+ compatible dependencies. For hosting environments running older PHP versions (like 8.0.30), the deployment process includes a platform override (`composer config platform.php 8.3.0`) to bypass version checks while maintaining functionality.

## Deployment Process:
1. Push changes to GitHub
2. Go to cPanel → Git™ Version Control → Pull or Deploy
3. Click "Deploy HEAD Commit" 
4. Your site will automatically update with optimized caches

## File Structure:
- **Repository**: `/home/beelkstc/tc/` (Git files)
- **Live Site**: `/home/beelkstc/public_html/` (Web accessible files)

## Environment:
Make sure `.env.production` contains your production database and app settings.

## Manual Pull vs Deploy:
- **Pull**: Updates Git repository only
- **Deploy**: Updates Git repository AND copies files to live site with optimization
