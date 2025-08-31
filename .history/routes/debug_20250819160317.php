<?php

use Illuminate\Support\Facades\Route;

Route::get('/debug/upload-limits', function () {
    return response()->json([
        'php_settings' => [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
        ],
        'converted_bytes' => [
            'upload_max_filesize' => (int)str_replace(['M', 'K', 'G'], ['000000', '000', '000000000'], ini_get('upload_max_filesize')),
            'post_max_size' => (int)str_replace(['M', 'K', 'G'], ['000000', '000', '000000000'], ini_get('post_max_size')),
        ],
        'request_info' => [
            'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 'not set',
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'not set',
        ],
        'server_info' => [
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'not set',
            'php_version' => PHP_VERSION,
        ]
    ]);
});
