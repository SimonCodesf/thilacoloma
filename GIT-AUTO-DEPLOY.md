# 🔄 Automatic Git Deployment Setup Guide

## Method 1: cPanel Git™ Version Control (Recommended)

### Step 1: Enable Git in cPanel
1. Log into your **Namecheap cPanel**
2. Look for **"Git™ Version Control"** in the Files section
3. Click **"Create"** to add a new repository

### Step 2: Connect to GitHub
1. **Repository URL**: `https://github.com/SimonCodesf/thilacoloma.git`
2. **Repository Path**: `/public_html/thilacoloma` (or your preferred folder)
3. **Branch**: `master`
4. Click **"Create"**

### Step 3: Set Up Auto-Deployment
1. In Git Version Control, click **"Manage"** next to your repo
2. Enable **"Auto Deploy"** 
3. Set **Deployment Script** to run: `./deploy.sh`

### Step 4: Configure Webhooks (Auto-sync)
1. Go to your GitHub repository settings
2. Click **"Webhooks"** → **"Add webhook"**
3. **Payload URL**: `https://yourdomain.com:2083/cpsess*/git/webhook.php`
4. **Content type**: `application/json`
5. **Events**: Select "Just the push event"

---

## Method 2: GitHub Actions Auto-Deploy (Advanced)

If cPanel Git isn't available, we can use GitHub Actions to auto-deploy.

### Benefits:
✅ **Automatic deployment** on every push to master
✅ **Build optimization** (composer install, cache clearing)
✅ **Error notifications** if deployment fails
✅ **Deploy logs** to track changes

---

## Method 3: Simple Webhook Script (Manual Setup)

Create a webhook endpoint that pulls changes when GitHub sends a notification.
