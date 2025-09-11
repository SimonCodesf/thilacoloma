<?php

echo "🔧 Direct PHP fix for Debugbar issue\n";

// Change to the correct directory
chdir('/var/www/thilacoloma');

echo "📍 Working directory: " . getcwd() . "\n";

// Clear all cache files
$cacheFiles = [
    'bootstrap/cache/config.php',
    'bootstrap/cache/routes-v7.php',
    'bootstrap/cache/services.php',
    'bootstrap/cache/packages.php',
    'storage/framework/cache/providers.php',
    'storage/framework/cache/services.php',
    'storage/framework/cache/packages.php',
];

foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "🗑️ Removed: $file\n";
    }
}

// Clear cache directories
$cacheDirs = [
    'storage/framework/cache/data',
    'storage/framework/views',
    'storage/framework/sessions',
    'storage/logs',
];

foreach ($cacheDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "🧹 Cleaned: $dir\n";
    }
}

echo "✅ Cache clearing complete!\n";
echo "🌐 Now try accessing: http://103.76.86.167/cp\n";
