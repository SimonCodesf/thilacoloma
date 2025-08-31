<?php

// Increase PHP limits programmatically
ini_set('upload_max_filesize', '50M');
ini_set('post_max_size', '50M');
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);
ini_set('max_input_time', 300);

// Enable error display for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Define the application base path
$app_path = '/home/beelkstc/tc_app';

// Check if essential files exist
if (!file_exists($app_path . '/vendor/autoload.php')) {
    die('<h1>Error: Missing Dependencies</h1><p>The vendor directory is missing. Please install Composer dependencies.</p><p>Run: <code>composer install --no-dev</code> in the application directory.</p>');
}

if (!file_exists($app_path . '/.env')) {
    die('<h1>Error: Missing Environment</h1><p>The .env file is missing. Please ensure environment configuration is deployed.</p>');
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $app_path.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $app_path.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
try {
    /** @var Application $app */
    $app = require_once $app_path.'/bootstrap/app.php';
    $app->handleRequest(Request::capture());
} catch (Exception $e) {
    echo '<h1>Application Error</h1>';
    echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p>File: ' . htmlspecialchars($e->getFile()) . '</p>';
    echo '<p>Line: ' . $e->getLine() . '</p>';
}
