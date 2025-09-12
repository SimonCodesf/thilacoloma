<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Listeners\UpdateSearchIndex;
use App\Listeners\AutoGitSyncListener;
use Statamic\Events\EntrySaved;
use Statamic\Events\EntryDeleted;
use Statamic\Events\AssetSaved;
use Statamic\Events\AssetDeleted;
use Statamic\Events\GlobalSaved;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        EntrySaved::class => [
            [UpdateSearchIndex::class, 'handleEntrySaved'],
            [AutoGitSyncListener::class, 'handleEntrySaved'],
        ],
        EntryDeleted::class => [
            [UpdateSearchIndex::class, 'handleEntryDeleted'],
            [AutoGitSyncListener::class, 'handleEntryDeleted'],
        ],
        AssetSaved::class => [
            [UpdateSearchIndex::class, 'handleAssetSaved'],
            [AutoGitSyncListener::class, 'handleAssetSaved'],
        ],
        AssetDeleted::class => [
            [UpdateSearchIndex::class, 'handleAssetDeleted'],
            [AutoGitSyncListener::class, 'handleAssetDeleted'],
        ],
        GlobalSaved::class => [
            [AutoGitSyncListener::class, 'handleGlobalSaved'],
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}