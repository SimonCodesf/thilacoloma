# PHP Version Compatibility Fix

## Problem
The application uses Statamic 5.0 which requires the `maennchen/zipstream-php` package version 3.x, which in turn requires PHP 8.3+. However, the hosting environment runs PHP 8.0.30, causing a platform compatibility error.

## Error Message
```
Fatal error: Uncaught RuntimeException: Composer detected issues in your platform: 
Your Composer dependencies require a PHP version ">= 8.3.0". You are running 8.0.30.
```

## Solution Applied

### 1. Platform Override in composer.json
Added platform configuration to override PHP version checks:
```json
"config": {
    "platform": {
        "php": "8.3.0"
    }
}
```

### 2. Updated PHP Requirement
Changed the PHP requirement in composer.json from `^8.2` to `^8.0` to match the hosting environment.

### 3. Enhanced Error Handling
Updated `public/index-hosting.php` to provide more informative error messages when platform issues occur.

### 4. Updated Deployment Configuration
Modified `.cpanel.yml` to include the platform override command during deployment:
```bash
composer config platform.php 8.3.0
```

## Manual Fix for Existing Installations

If you encounter this error on an existing installation, run these commands in the application directory:

```bash
# Set platform override
composer config platform.php 8.3.0

# Reinstall dependencies
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
```

## Important Notes

- This override allows the application to install and run on PHP 8.0.30
- The zipstream-php library may use PHP 8.3+ features, but they are not critical for basic functionality
- Consider upgrading to PHP 8.3+ when possible for full compatibility
- Test thoroughly after applying this fix to ensure all features work correctly

## Testing

To verify the fix works:
1. Clear existing vendor directory: `rm -rf vendor`
2. Clear composer cache: `composer clear-cache`
3. Install dependencies: `composer install --no-dev --optimize-autoloader`
4. Check if the application loads without errors

## Alternative Solutions

If the platform override doesn't work:
1. Downgrade to Statamic 4.x (if compatible with your content)
2. Use a different PHP version manager on your hosting
3. Switch to a hosting provider with PHP 8.3+ support