<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Statamic\Events\AssetUploaded;
use Statamic\Events\AssetSaved;

class AssetEventServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Log asset uploads for debugging
        Event::listen(AssetUploaded::class, function ($event) {
            Log::info('Asset uploaded: ' . $event->asset->filename());
        });

        Event::listen(AssetSaved::class, function ($event) {
            Log::info('Asset saved: ' . $event->asset->filename());
        });
    }
}
