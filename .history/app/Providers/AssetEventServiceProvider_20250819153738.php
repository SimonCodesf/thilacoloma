<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Statamic\Events\AssetUploaded;
use Statamic\Events\AssetSaved;
use Illuminate\Support\Facades\Log;

class AssetEventServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Log asset uploads for debugging
        AssetUploaded::listen(function ($event) {
            Log::info('Asset uploaded: ' . $event->asset->filename());
        });

        AssetSaved::listen(function ($event) {
            Log::info('Asset saved: ' . $event->asset->filename());
        });
    }
}
