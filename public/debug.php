<?php
// Simple error diagnostic script
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Thila Coloma - Diagnostic Script</h1>";
echo "<h2>PHP Information</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current Directory: " . getcwd() . "<br>";
echo "Script Path: " . __FILE__ . "<br>";

echo "<h2>Application Path Check</h2>";
$app_path = '/home/beelkstc/tc_app';
echo "App Path: $app_path<br>";
echo "App Path Exists: " . (file_exists($app_path) ? 'YES' : 'NO') . "<br>";

echo "<h2>Key Files Check</h2>";
$files_to_check = [
    $app_path . '/vendor/autoload.php',
    $app_path . '/bootstrap/app.php',
    $app_path . '/.env',
    $app_path . '/storage',
    $app_path . '/database/database.sqlite'
];

foreach ($files_to_check as $file) {
    echo basename($file) . ": " . (file_exists($file) ? 'EXISTS' : 'MISSING') . "<br>";
}

echo "<h2>Directory Contents</h2>";
if (file_exists($app_path)) {
    echo "Contents of $app_path:<br>";
    $contents = scandir($app_path);
    foreach ($contents as $item) {
        if ($item !== '.' && $item !== '..') {
            echo "- $item<br>";
        }
    }
} else {
    echo "App directory does not exist!<br>";
}

echo "<h2>Environment File</h2>";
$env_file = $app_path . '/.env';
if (file_exists($env_file)) {
    echo "Environment file exists. Size: " . filesize($env_file) . " bytes<br>";
    echo "First few lines:<br><pre>";
    $lines = file($env_file);
    echo htmlspecialchars(implode('', array_slice($lines, 0, 5)));
    echo "</pre>";
} else {
    echo "Environment file is missing!<br>";
}

try {
    echo "<h2>Autoloader Test</h2>";
    if (file_exists($app_path . '/vendor/autoload.php')) {
        require_once $app_path . '/vendor/autoload.php';
        echo "Autoloader loaded successfully<br>";
    } else {
        echo "Autoloader file not found<br>";
    }
} catch (Exception $e) {
    echo "Error loading autoloader: " . $e->getMessage() . "<br>";
}

echo "<h2>Done</h2>";
?>
