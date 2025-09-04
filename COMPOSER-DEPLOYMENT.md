# Composer Deployment Configuration

## Common Issues and Solutions

### ⚠️ Timeout Configuration

**WRONG:** `composer install --timeout=300`  
❌ The `--timeout` flag does NOT exist in composer commands.

**CORRECT:** Use composer config instead:
```bash
# Set process timeout to 10 minutes (600 seconds)
composer config process-timeout 600

# Then run install normally
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
```

### 🔧 Recommended Deployment Configuration

For production deployments, use these composer settings:

```bash
# Configure timeouts and plugins
composer config process-timeout 600
composer config --no-plugins allow-plugins.php-http/discovery true
composer config --no-plugins allow-plugins.pixelfear/composer-dist-plugin true
composer config --no-plugins allow-plugins.pestphp/pest-plugin true

# Install dependencies optimized for production
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress
```

### 🌐 Network Issues (guzzlehttp/promises)

If you get errors like "git reference for guzzlehttp/promises is not in cache and network is disabled":

1. **Ensure composer.lock is committed**: This file contains exact versions and reduces network requests.

2. **Use `--prefer-dist` flag**: This downloads packages as ZIP files instead of cloning git repositories:
   ```bash
   composer install --prefer-dist --no-dev --optimize-autoloader
   ```

3. **Configure composer to use archives**: 
   ```bash
   composer config preferred-install dist
   ```

4. **For hosting environments with limited network access**:
   - Make sure the `composer.lock` file is up to date
   - Consider using composer's `--no-suggest` flag to reduce network calls
   - Use cPanel or hosting provider's deployment tools that have better network access

### 📝 Valid Composer Install Options

Here are the valid options for `composer install`:

- `--prefer-source`: Clone from source repos (requires git/network)
- `--prefer-dist`: Download ZIP archives (faster, less network intensive)
- `--no-dev`: Skip development dependencies  
- `--no-autoloader`: Skip autoloader generation
- `--optimize-autoloader`: Generate optimized autoloader
- `--classmap-authoritative`: Use authoritative class maps
- `--no-interaction`: Do not prompt for input
- `--no-progress`: Hide progress indicators
- `--no-suggest`: Skip suggested package recommendations

### 🚀 Deployment Scripts

All deployment scripts have been updated to use proper composer configuration:

- `.github/workflows/deploy.yml` - GitHub Actions deployment
- `.github/workflows/deploy-fixed.yml` - Fixed GitHub Actions deployment  
- `.cpanel.yml` - cPanel deployment configuration
- `deploy.sh` - Manual deployment script
- `webhook-deploy.php` - Webhook-triggered deployment

## Environment-Specific Notes

### GitHub Actions
- PHP and composer are pre-configured
- Network access is available
- Use `--prefer-dist` for faster installs

### cPanel/Shared Hosting
- Network access may be limited
- Composer timeout configuration is essential
- Always use `composer.lock` for consistent installs

### Manual Deployment
- Run `composer config process-timeout 600` before installation
- Use `--prefer-dist` to avoid git repository requirements
- Ensure proper file permissions after installation