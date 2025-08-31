<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Asset Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure the maximum file sizes and upload settings
    | for asset uploads in Statamic.
    |
    */

    'max_upload_size' => '50M',
    
    'allowed_extensions' => [
        'jpg', 'jpeg', 'png', 'gif', 'svg', 'webp',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        'mp4', 'mov', 'avi', 'wmv', 'flv',
        'mp3', 'wav', 'aac', 'flac',
        'zip', 'rar', '7z', 'tar', 'gz'
    ],

];
