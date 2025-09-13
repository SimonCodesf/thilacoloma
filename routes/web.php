<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\LiveSearchController;

// Explicit home route
Route::statamic('/', 'home', [
   'title' => 'Thila Coloma',
   'template' => 'home',
   'layout' => 'layouts/app'
]);

// Route::statamic('example', 'example-view', [
//    'title' => 'Example'
// ]);

Route::statamic('test-globals', 'test-globals', [
   'title' => 'Test Globals'
]);

// Calendar proxy route to avoid CORS issues
Route::get('/api/calendar/feed', [CalendarController::class, 'getCalendarFeed']);

// Live search API endpoint
Route::get('/api/search', [LiveSearchController::class, 'search']);
