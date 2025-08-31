# cPanel Deployment Configuration

This `.cpanel.yml` file enables automatic deployment through cPanel's Git™ Version Control.

## What it does:
1. Copies all files from the Git repository to your public web directory
2. Copies `.env.production` to `.env` for production settings
3. Installs composer dependencies (production optimized)
4. Clears and optimizes Laravel/Statamic caches
5. Sets proper file permissions

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
