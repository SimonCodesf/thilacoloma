<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default search index
    |--------------------------------------------------------------------------
    |
    | This option controls the search index that gets queried when performing
    | search functions without explicitly selecting another index.
    |
    */

    'default' => env('STATAMIC_DEFAULT_SEARCH_INDEX', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Search Indexes
    |--------------------------------------------------------------------------
    |
    | Here you can define all of the available search indexes.
    |
    */

    'indexes' => [

        'default' => [
            'driver' => 'local',
            'searchables' => 'all',
            'fields' => ['title', 'content', 'intro_text', 'intro_title', 'description', 'tak_beschrijving', 'tak_activiteiten', 'meetings_description', 'weekends_beschrijving', 'kampen_binnenlands_tekst', 'kampen_buitenlands_tekst'],
        ],

        // 'blog' => [
        //     'driver' => 'local',
        //     'searchables' => 'collection:blog',
        // ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Driver Defaults
    |--------------------------------------------------------------------------
    |
    | Here you can specify default configuration to be applied to all indexes
    | that use the corresponding driver. For instance, if you have two
    | indexes that use the "local" driver, both of them can have the
    | same base configuration. You may override for each index.
    |
    */

    'drivers' => [

        'local' => [
            'path' => storage_path('statamic/search'),
        ],

        'algolia' => [
            'credentials' => [
                'id' => env('ALGOLIA_APP_ID', ''),
                'secret' => env('ALGOLIA_SECRET', ''),
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Search Defaults
    |--------------------------------------------------------------------------
    |
    | Here you can specify default configuration to be applied to all indexes
    | regardless of the driver. You can override these per driver or per index.
    |
    */

    'defaults' => [
        'fields' => ['title'],
    ],

];
