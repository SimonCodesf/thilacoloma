<?php

namespace App\Listeners;

use Statamic\Events\EntrySaved;
use Statamic\Events\EntryDeleted;
use Statamic\Events\AssetSaved;
use Statamic\Events\AssetDeleted;
use Statamic\Facades\Search;
use Illuminate\Support\Facades\Log;

class UpdateSearchIndex
{
    /**
     * Handle the entry saved event.
     */
    public function handleEntrySaved(EntrySaved $event)
    {
        $this->updateSearchIndex('Entry saved: ' . $event->entry->title());
    }

    /**
     * Handle the entry deleted event.
     */
    public function handleEntryDeleted(EntryDeleted $event)
    {
        $this->updateSearchIndex('Entry deleted');
    }

    /**
     * Handle the asset saved event.
     */
    public function handleAssetSaved(AssetSaved $event)
    {
        $this->updateSearchIndex('Asset saved: ' . $event->asset->filename());
    }

    /**
     * Handle the asset deleted event.
     */
    public function handleAssetDeleted(AssetDeleted $event)
    {
        $this->updateSearchIndex('Asset deleted');
    }

    /**
     * Update the search index
     */
    private function updateSearchIndex($reason)
    {
        try {
            Search::index('default')->update();
            Log::info("Search index updated automatically: {$reason}");
        } catch (\Exception $e) {
            Log::error("Failed to update search index: {$e->getMessage()}");
        }
    }
}