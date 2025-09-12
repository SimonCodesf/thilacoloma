<?php

/**
 * Auto Git Sync Webhook - Public Web Endpoint
 * 
 * This is the public-facing webhook endpoint that can be called
 * to trigger automatic Git synchronization.
 */

// Include the main auto-sync functionality
require_once __DIR__ . '/../auto-git-sync.php';