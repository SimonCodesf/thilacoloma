<?php

/**
 * Automatic Git Sync for Statamic Content Changes
 * 
 * This script automatically commits and pushes content changes to GitHub
 * when triggered by Statamic events or manual webhook calls.
 */

// Configuration
const LOG_FILE = __DIR__ . '/storage/logs/auto-git-sync.log';
const LOCK_FILE = __DIR__ . '/storage/logs/git-sync.lock';
const MAX_LOCK_TIME = 300; // 5 minutes
const GIT_COMMIT_MESSAGE_PREFIX = '[AUTO-SYNC]';

// Security token (you should set this in your environment)
$WEBHOOK_TOKEN = $_ENV['WEBHOOK_TOKEN'] ?? 'your-secure-token-here';

/**
 * Log messages with timestamp
 */
function logMessage($message, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message\n";
    
    // Ensure logs directory exists
    $logDir = dirname(LOG_FILE);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents(LOG_FILE, $logEntry, FILE_APPEND | LOCK_EX);
    
    // Also output to response if this is a web request
    if (php_sapi_name() !== 'cli') {
        echo $logEntry;
    }
}

/**
 * Check if another sync process is running
 */
function acquireLock() {
    if (file_exists(LOCK_FILE)) {
        $lockTime = filemtime(LOCK_FILE);
        if (time() - $lockTime < MAX_LOCK_TIME) {
            logMessage("Another sync process is running (lock file exists)", "WARNING");
            return false;
        } else {
            // Lock file is stale, remove it
            unlink(LOCK_FILE);
            logMessage("Removed stale lock file", "INFO");
        }
    }
    
    // Create lock file
    file_put_contents(LOCK_FILE, getmypid());
    return true;
}

/**
 * Release the lock
 */
function releaseLock() {
    if (file_exists(LOCK_FILE)) {
        unlink(LOCK_FILE);
    }
}

/**
 * Execute git commands safely
 */
function executeGitCommand($command, $workingDir = null) {
    if ($workingDir) {
        $command = "cd " . escapeshellarg($workingDir) . " && " . $command;
    }
    
    logMessage("Executing: $command", "DEBUG");
    
    $output = [];
    $returnCode = 0;
    exec($command . " 2>&1", $output, $returnCode);
    
    $outputString = implode("\n", $output);
    
    if ($returnCode === 0) {
        logMessage("Command succeeded: $outputString", "DEBUG");
    } else {
        logMessage("Command failed (code $returnCode): $outputString", "ERROR");
    }
    
    return [
        'success' => $returnCode === 0,
        'output' => $outputString,
        'code' => $returnCode
    ];
}

/**
 * Check if there are any changes to commit
 */
function hasGitChanges() {
    $result = executeGitCommand("git status --porcelain", __DIR__);
    return $result['success'] && !empty(trim($result['output']));
}

/**
 * Get a summary of changes for commit message
 */
function getChangeSummary() {
    $result = executeGitCommand("git status --porcelain", __DIR__);
    if (!$result['success']) {
        return "Content changes";
    }
    
    $lines = array_filter(explode("\n", trim($result['output'])));
    $changes = [];
    $addedFiles = 0;
    $modifiedFiles = 0;
    $deletedFiles = 0;
    
    foreach ($lines as $line) {
        $status = substr($line, 0, 2);
        $file = trim(substr($line, 2));
        
        if (strpos($status, 'A') !== false) $addedFiles++;
        if (strpos($status, 'M') !== false) $modifiedFiles++;
        if (strpos($status, 'D') !== false) $deletedFiles++;
        
        // Track specific content changes
        if (strpos($file, 'content/') === 0) {
            $changes[] = basename($file);
        }
    }
    
    $summary = [];
    if ($addedFiles > 0) $summary[] = "$addedFiles added";
    if ($modifiedFiles > 0) $summary[] = "$modifiedFiles modified";
    if ($deletedFiles > 0) $summary[] = "$deletedFiles deleted";
    
    $changeSummary = implode(', ', $summary);
    
    if (!empty($changes)) {
        $changeList = implode(', ', array_slice($changes, 0, 3));
        if (count($changes) > 3) {
            $changeList .= " and " . (count($changes) - 3) . " more";
        }
        return "$changeSummary: $changeList";
    }
    
    return $changeSummary ?: "Content updates";
}

/**
 * Perform the git sync
 */
function performGitSync($trigger = 'manual', $user = 'system') {
    logMessage("=== Starting Git Sync (triggered by: $trigger, user: $user) ===", "INFO");
    
    // Check if we have any changes
    if (!hasGitChanges()) {
        logMessage("No changes to commit", "INFO");
        return ['success' => true, 'message' => 'No changes to sync'];
    }
    
    // Add all changes
    $result = executeGitCommand("git add .", __DIR__);
    if (!$result['success']) {
        logMessage("Failed to add changes: " . $result['output'], "ERROR");
        return ['success' => false, 'message' => 'Failed to stage changes'];
    }
    
    // Create commit message
    $changeSummary = getChangeSummary();
    $timestamp = date('Y-m-d H:i:s');
    $commitMessage = GIT_COMMIT_MESSAGE_PREFIX . " $changeSummary (by $user at $timestamp)";
    
    // Commit changes
    $result = executeGitCommand("git commit -m " . escapeshellarg($commitMessage), __DIR__);
    if (!$result['success']) {
        logMessage("Failed to commit changes: " . $result['output'], "ERROR");
        return ['success' => false, 'message' => 'Failed to commit changes'];
    }
    
    logMessage("Committed changes: $changeSummary", "INFO");
    
    // Push to remote
    $result = executeGitCommand("git push origin HEAD", __DIR__);
    if (!$result['success']) {
        logMessage("Failed to push changes: " . $result['output'], "ERROR");
        return ['success' => false, 'message' => 'Failed to push to remote'];
    }
    
    logMessage("Successfully pushed changes to GitHub", "INFO");
    logMessage("=== Git Sync Complete ===", "INFO");
    
    return [
        'success' => true,
        'message' => "Successfully synced: $changeSummary",
        'commit_message' => $commitMessage
    ];
}

// Main execution logic
try {
    // Set content type for web requests
    if (php_sapi_name() !== 'cli') {
        header('Content-Type: text/plain');
    }
    
    // Check authentication for web requests
    if (php_sapi_name() !== 'cli') {
        $providedToken = $_GET['token'] ?? $_POST['token'] ?? '';
        if ($providedToken !== $WEBHOOK_TOKEN) {
            http_response_code(401);
            logMessage("Unauthorized access attempt", "WARNING");
            exit("Unauthorized\n");
        }
    }
    
    // Acquire lock to prevent concurrent executions
    if (!acquireLock()) {
        http_response_code(409);
        exit("Another sync process is running\n");
    }
    
    // Get trigger information
    $trigger = $_GET['trigger'] ?? $_POST['trigger'] ?? 'manual';
    $user = $_GET['user'] ?? $_POST['user'] ?? 'unknown';
    
    // Perform the sync
    $result = performGitSync($trigger, $user);
    
    // Return result
    if ($result['success']) {
        http_response_code(200);
        echo "SUCCESS: " . $result['message'] . "\n";
    } else {
        http_response_code(500);
        echo "ERROR: " . $result['message'] . "\n";
    }
    
} catch (Exception $e) {
    logMessage("Exception during sync: " . $e->getMessage(), "ERROR");
    http_response_code(500);
    echo "ERROR: " . $e->getMessage() . "\n";
} finally {
    // Always release the lock
    releaseLock();
}

?>