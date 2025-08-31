<?php

use Illuminate\Support\Facades\Route;
use Statamic\Statamic;

// Homepage route
Route::statamic('/', 'home', [
    'title' => 'Home',
    'id' => 'home'
]);

// Route::statamic('example', 'example-view', [
//    'title' => 'Example'
// ]);
