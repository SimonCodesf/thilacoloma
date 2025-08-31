<?php

use Illuminate\Support\Facades\Route;

Route::get('/debug/upload-limits', function () {
    return response()->json([
        'php_settings' => [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'max_input_time' => ini_get('max_input_time'),
            'file_uploads' => ini_get('file_uploads'),
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
            'sapi' => php_sapi_name(),
            'loaded_ini' => php_ini_loaded_file(),
        ]
    ]);
});

Route::post('/debug/test-upload', function () {
    $uploadedFile = request()->file('file');
    
    return response()->json([
        'success' => true,
        'file_info' => $uploadedFile ? [
            'original_name' => $uploadedFile->getClientOriginalName(),
            'size' => $uploadedFile->getSize(),
            'mime_type' => $uploadedFile->getMimeType(),
        ] : null,
        'post_data_size' => strlen(http_build_query($_POST)),
        'files_count' => count($_FILES),
        'php_errors' => error_get_last(),
    ]);
});
