<?php
/**
 * Thila Coloma - Namecheap Setup Helper
 * Upload this file to your hosting and visit it in browser to set up the website
 */

echo "<h1>🏕️ Thila Coloma - Namecheap Setup</h1>";

// Check PHP version
echo "<h2>📋 System Requirements</h2>";
$phpVersion = phpversion();
echo "<p>PHP Version: <strong>$phpVersion</strong> ";
if (version_compare($phpVersion, '8.1.0', '>=')) {
    echo "✅ Good</p>";
} else {
    echo "❌ Need PHP 8.1+</p>";
}

// Check required extensions
$required = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json', 'curl', 'gd'];
echo "<h3>Required PHP Extensions:</h3><ul>";
foreach ($required as $ext) {
    echo "<li>$ext: " . (extension_loaded($ext) ? "✅" : "❌") . "</li>";
}
echo "</ul>";

// Check file permissions
echo "<h2>📁 File Permissions</h2>";
$paths = [
    'storage' => is_writable(__DIR__ . '/storage'),
    'bootstrap/cache' => is_writable(__DIR__ . '/bootstrap/cache'),
    '.env' => file_exists(__DIR__ . '/.env')
];

foreach ($paths as $path => $status) {
    echo "<p>$path: " . ($status ? "✅" : "❌") . "</p>";
}

// Environment setup
echo "<h2>⚙️ Environment Setup</h2>";
if (!file_exists('.env')) {
    if (file_exists('.env.production')) {
        copy('.env.production', '.env');
        echo "<p>✅ Created .env from .env.production</p>";
    } else {
        echo "<p>❌ No .env.production file found. Please create .env manually.</p>";
    }
} else {
    echo "<p>✅ .env file exists</p>";
}

// Database connection test
if (file_exists('.env')) {
    echo "<h2>🗄️ Database Connection</h2>";
    $env = parse_ini_file('.env');
    
    if (isset($env['DB_HOST'], $env['DB_DATABASE'], $env['DB_USERNAME'])) {
        try {
            $pdo = new PDO(
                "mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']}",
                $env['DB_USERNAME'],
                $env['DB_PASSWORD'] ?? ''
            );
            echo "<p>✅ Database connection successful</p>";
        } catch (PDOException $e) {
            echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>";
            echo "<p>📝 Please update your .env file with correct database credentials from Namecheap cPanel.</p>";
        }
    } else {
        echo "<p>⚠️ Database credentials not configured in .env</p>";
    }
}

// Next steps
echo "<h2>🚀 Next Steps</h2>";
echo "<ol>";
echo "<li>Fix any ❌ issues above</li>";
echo "<li>Update .env file with your domain and database details</li>";
echo "<li>Run: <code>php artisan key:generate</code></li>";
echo "<li>Run: <code>php artisan migrate</code></li>";
echo "<li>Visit: <a href='/cp'>/cp</a> to access Statamic</li>";
echo "<li>Login with: antilope@thilacoloma.be</li>";
echo "</ol>";

// Generate application key button
echo "<h2>🔑 Generate Application Key</h2>";
if (isset($_POST['generate_key'])) {
    if (file_exists('.env') && function_exists('exec')) {
        exec('php artisan key:generate 2>&1', $output, $return);
        if ($return === 0) {
            echo "<p>✅ Application key generated successfully</p>";
        } else {
            echo "<p>❌ Failed to generate key. Please run manually: <code>php artisan key:generate</code></p>";
        }
    }
}

echo "<form method='post'>";
echo "<button type='submit' name='generate_key' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Generate App Key</button>";
echo "</form>";

echo "<hr>";
echo "<p><small>After setup is complete, delete this file for security.</small></p>";
?>
