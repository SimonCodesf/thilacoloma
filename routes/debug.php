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
    $allFiles = request()->allFiles();
    $rawFiles = $_FILES;
    $postData = $_POST;
    $error = null;
    $fileInfo = null;
    if ($uploadedFile) {
        try {
            $fileInfo = [
                'original_name' => $uploadedFile->getClientOriginalName(),
                'size' => $uploadedFile->getSize(),
                'mime_type' => $uploadedFile->getMimeType(),
                'is_valid' => $uploadedFile->isValid(),
                'path' => $uploadedFile->getRealPath(),
            ];
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    } else {
        $error = 'No file received by Laravel. Check PHP limits and frontend.';
    }
    return response()->json([
        'success' => (bool)$uploadedFile,
        'file_info' => $fileInfo,
        'error' => $error,
        'all_files' => $allFiles,
        'raw_files' => $rawFiles,
        'post_data' => $postData,
        'post_data_size' => strlen(http_build_query($postData)),
        'files_count' => count($rawFiles),
        'php_errors' => error_get_last(),
    ]);
});

Route::get('/test-upload', function () {
    return view('test-upload');
});
