# GitHub Webhook Auto-Deploy Setup

This is the simplest and most reliable method for automatic deployment from GitHub to Namecheap hosting.

## Setup Instructions

### 1. Upload the Webhook Script
- Upload `webhook-deploy.php` to your Namecheap hosting root directory
- Make sure it's accessible at: `https://yourdomain.com/webhook-deploy.php`

### 2. Configure the Script
Edit `webhook-deploy.php` and update:
```php
// Change this to a secure random string
$secret = 'your_webhook_secret_here';

// Update to your actual hosting path
chdir('/home/your_username/public_html');
```

### 3. Setup GitHub Webhook
1. Go to your GitHub repository: https://github.com/SimonCodesf/thilacoloma
2. Click **Settings** → **Webhooks** → **Add webhook**
3. Configure:
   - **Payload URL**: `https://yourdomain.com/webhook-deploy.php`
   - **Content type**: `application/json`
   - **Secret**: Use the same secret from your PHP script
   - **Events**: Select "Just the push event"
   - **Active**: ✓ Checked

### 4. Test the Setup
1. Make a small change to your repository
2. Push to the master branch
3. Check `https://yourdomain.com/deploy.log` for deployment logs

## How It Works

1. You push changes to GitHub
2. GitHub calls your webhook script
3. Script verifies the request and pulls latest code
4. Runs Statamic optimization commands
5. Logs the deployment process

## Advantages

✅ **Simple**: Just one PHP file  
✅ **Reliable**: Direct HTTP webhook from GitHub  
✅ **Secure**: Signature verification  
✅ **Logged**: All deployments are logged  
✅ **Fast**: Immediate deployment on push  

## Troubleshooting

- Check `deploy.log` for error messages
- Ensure your hosting supports `exec()` function
- Verify file permissions (755 for directories, 644 for files)
- Make sure Git is available on your hosting

## Alternative: Manual FTP

If webhooks don't work, you can use GitHub Actions (see `.github/workflows/deploy.yml`) to automatically FTP files to your hosting.
