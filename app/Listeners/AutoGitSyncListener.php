<?php

namespace App\Listeners;

use Statamic\Events\EntrySaved;
use Statamic\Events\EntryDeleted;
use Statamic\Events\GlobalSaved;
use Statamic\Events\AssetSaved;
use Statamic\Events\AssetDeleted;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AutoGitSyncListener
{
    /**
     * Handle entry saved events
     */
    public function handleEntrySaved(EntrySaved $event)
    {
        $this->triggerGitSync('entry_saved', [
            'type' => 'entry',
            'action' => 'saved',
            'collection' => $event->entry->collection()->handle(),
            'slug' => $event->entry->slug(),
            'title' => $event->entry->get('title', $event->entry->slug()),
            'user' => auth()->user()->email ?? 'system'
        ]);
    }
    
    /**
     * Handle entry deleted events
     */
    public function handleEntryDeleted(EntryDeleted $event)
    {
        $this->triggerGitSync('entry_deleted', [
            'type' => 'entry',
            'action' => 'deleted',
            'collection' => $event->entry->collection()->handle(),
            'slug' => $event->entry->slug(),
            'title' => $event->entry->get('title', $event->entry->slug()),
            'user' => auth()->user()->email ?? 'system'
        ]);
    }
    
    /**
     * Handle global saved events
     */
    public function handleGlobalSaved(GlobalSaved $event)
    {
        $this->triggerGitSync('global_saved', [
            'type' => 'global',
            'action' => 'saved',
            'handle' => $event->global->handle(),
            'user' => auth()->user()->email ?? 'system'
        ]);
    }
    
    /**
     * Handle asset saved events
     */
    public function handleAssetSaved(AssetSaved $event)
    {
        $this->triggerGitSync('asset_saved', [
            'type' => 'asset',
            'action' => 'saved',
            'filename' => $event->asset->filename(),
            'container' => $event->asset->container()->handle(),
            'user' => auth()->user()->email ?? 'system'
        ]);
    }
    
    /**
     * Handle asset deleted events
     */
    public function handleAssetDeleted(AssetDeleted $event)
    {
        $this->triggerGitSync('asset_deleted', [
            'type' => 'asset',
            'action' => 'deleted',
            'filename' => $event->asset->filename(),
            'container' => $event->asset->container()->handle(),
            'user' => auth()->user()->email ?? 'system'
        ]);
    }
    
    /**
     * Trigger the git sync webhook
     */
    protected function triggerGitSync(string $trigger, array $context = [])
    {
        try {
            // Get webhook configuration
            $webhookUrl = config('app.git_sync_webhook_url', url('/auto-git-sync.php'));
            $webhookToken = config('app.git_sync_webhook_token', env('WEBHOOK_TOKEN', 'your-secure-token-here'));
            
            // Prepare webhook payload
            $payload = [
                'trigger' => $trigger,
                'user' => $context['user'] ?? 'system',
                'token' => $webhookToken,
                'timestamp' => now()->toISOString(),
                'context' => $context
            ];
            
            Log::info('Triggering auto git sync', $payload);
            
            // Use async HTTP request to avoid blocking the user interface
            // Make the request with a short timeout
            $response = Http::timeout(10)->asForm()->post($webhookUrl, $payload);
            
            if ($response->successful()) {
                Log::info('Auto git sync triggered successfully', [
                    'trigger' => $trigger,
                    'response' => $response->body()
                ]);
            } else {
                Log::warning('Auto git sync webhook failed', [
                    'trigger' => $trigger,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Exception in auto git sync listener', [
                'trigger' => $trigger,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}