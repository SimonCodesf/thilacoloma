<?php

/**
 * Auto Git Sync Status Dashboard
 * Simple admin interface to monitor git sync status and logs
 */

// Security token check
$WEBHOOK_TOKEN = $_ENV['WEBHOOK_TOKEN'] ?? 'thila-coloma-auto-sync-2025';
$providedToken = $_GET['token'] ?? '';

if ($providedToken !== $WEBHOOK_TOKEN) {
    http_response_code(401);
    exit("Unauthorized access");
}

$logFile = __DIR__ . '/../storage/logs/auto-git-sync.log';
$lockFile = __DIR__ . '/../storage/logs/git-sync.lock';

// Handle actions
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'clear_logs') {
    if (file_exists($logFile)) {
        file_put_contents($logFile, '');
        $message = "Logs cleared successfully";
    }
}

if ($action === 'manual_sync') {
    $webhookUrl = "http://localhost/auto-git-sync.php";
    $response = file_get_contents($webhookUrl . "?token=" . urlencode($WEBHOOK_TOKEN) . "&trigger=manual&user=admin");
    $message = "Manual sync triggered: " . $response;
}

// Get current status
$hasLock = file_exists($lockFile);
$lastLogs = '';
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    $logLines = array_filter(explode("\n", $logs));
    $lastLogs = implode("\n", array_slice($logLines, -50)); // Last 50 lines
}

// Get git status
$gitStatus = shell_exec('cd ' . escapeshellarg(__DIR__ . '/..') . ' && git status --porcelain 2>&1');
$hasChanges = !empty(trim($gitStatus));

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Git Sync Status - Thila Coloma</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #3747a0;
            border-bottom: 2px solid #3747a0;
            padding-bottom: 10px;
        }
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .status-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #3747a0;
        }
        .status-card.warning {
            border-left-color: #ffa500;
        }
        .status-card.error {
            border-left-color: #dc3545;
        }
        .status-card.success {
            border-left-color: #28a745;
        }
        .status-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .status-value {
            font-size: 14px;
            color: #666;
        }
        .logs-section {
            margin-top: 30px;
        }
        .logs-textarea {
            width: 100%;
            height: 300px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            padding: 15px;
            background: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .actions {
            margin: 20px 0;
            display: flex;
            gap: 10px;
        }
        .btn {
            background: #3747a0;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        .btn:hover {
            background: #2a3875;
        }
        .btn.warning {
            background: #ffa500;
        }
        .btn.warning:hover {
            background: #cc8400;
        }
        .message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .timestamp {
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 Auto Git Sync Status</h1>
        
        <?php if (isset($message)): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <div class="status-grid">
            <div class="status-card <?= $hasLock ? 'warning' : 'success' ?>">
                <div class="status-title">🔒 Sync Status</div>
                <div class="status-value">
                    <?= $hasLock ? 'Sync in progress...' : 'Ready for sync' ?>
                    <?php if ($hasLock): ?>
                        <br><small>Lock file: <?= date('Y-m-d H:i:s', filemtime($lockFile)) ?></small>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="status-card <?= $hasChanges ? 'warning' : 'success' ?>">
                <div class="status-title">📝 Git Changes</div>
                <div class="status-value">
                    <?= $hasChanges ? 'Uncommitted changes detected' : 'No pending changes' ?>
                    <?php if ($hasChanges): ?>
                        <br><small><?= nl2br(htmlspecialchars(trim($gitStatus))) ?></small>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="status-card">
                <div class="status-title">📊 Log File</div>
                <div class="status-value">
                    <?php if (file_exists($logFile)): ?>
                        Size: <?= number_format(filesize($logFile)) ?> bytes<br>
                        Last modified: <?= date('Y-m-d H:i:s', filemtime($logFile)) ?>
                    <?php else: ?>
                        No log file found
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="status-card">
                <div class="status-title">⚙️ Configuration</div>
                <div class="status-value">
                    Token: <?= substr($WEBHOOK_TOKEN, 0, 8) ?>...<br>
                    Webhook: /auto-git-sync.php<br>
                    <small class="timestamp">Last check: <?= date('Y-m-d H:i:s') ?></small>
                </div>
            </div>
        </div>
        
        <div class="actions">
            <form method="post" style="display: inline;">
                <input type="hidden" name="action" value="manual_sync">
                <button type="submit" class="btn">🔄 Trigger Manual Sync</button>
            </form>
            
            <a href="?token=<?= urlencode($WEBHOOK_TOKEN) ?>" class="btn">🔃 Refresh Status</a>
            
            <?php if (file_exists($logFile) && filesize($logFile) > 0): ?>
                <form method="post" style="display: inline;">
                    <input type="hidden" name="action" value="clear_logs">
                    <button type="submit" class="btn warning" onclick="return confirm('Clear all logs?')">🗑️ Clear Logs</button>
                </form>
            <?php endif; ?>
        </div>
        
        <div class="logs-section">
            <h2>📋 Recent Logs</h2>
            <textarea class="logs-textarea" readonly><?= htmlspecialchars($lastLogs) ?></textarea>
        </div>
        
        <script>
            // Auto-refresh every 30 seconds
            setTimeout(() => {
                window.location.reload();
            }, 30000);
        </script>
    </div>
</body>
</html>