<?php

// Increase PHP limits programmatically
ini_set('upload_max_filesize', '50M');
ini_set('post_max_size', '50M');
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);
ini_set('max_input_time', 300);

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Define the application base path
$app_path = '/home/beelkstc/tc_app';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $app_path.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $app_path.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $app_path.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
