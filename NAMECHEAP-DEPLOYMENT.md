# Thila Coloma - Namecheap Deployment Guide

## 📋 Pre-Deployment Checklist

### ✅ Requirements
- [ ] Namecheap hosting with PHP 8.1+ support
- [ ] MySQL database access
- [ ] cPanel or file manager access
- [ ] Domain/subdomain configured

## 🚀 Deployment Steps

### Step 1: Download Website Files
1. Download your repository as ZIP from GitHub
2. Extract the files locally

### Step 2: Prepare Environment File
1. Rename `.env.production` to `.env`
2. Update the following settings in `.env`:

```bash
APP_URL=https://yourdomain.com
DB_HOST=localhost (or your Namecheap DB host)
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

### Step 3: Upload Files
1. Upload ALL files to your hosting directory
2. Make sure `public` folder is your document root
3. Set permissions: 755 for folders, 644 for files
4. Set 777 permissions for: `storage/` and `bootstrap/cache/`

### Step 4: Database Setup
1. Create MySQL database in cPanel
2. Import any existing data (if needed)
3. Run: `php artisan migrate` (via SSH or cron)

### Step 5: Generate Application Key
Run in terminal (or via cPanel Terminal):
```bash
php artisan key:generate
```

### Step 6: Optimize for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan statamic:stache:warm
```

### Step 7: Test Access
- Visit: `yourdomain.com/cp`
- Login with: `antilope@thilacoloma.be`

## 🔧 Troubleshooting

### Common Issues:
1. **500 Error**: Check file permissions
2. **Database Connection**: Verify DB credentials
3. **Missing Dependencies**: Run `composer install --no-dev`

### Support Files Created:
- `namecheap-setup.php` - Automatic setup script
- `check-requirements.php` - System requirements checker

## 📞 Need Help?
Contact: coelho@thilacoloma.be
