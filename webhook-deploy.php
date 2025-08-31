<?php
/**
 * GitHub Webhook Auto-Deploy Script
 * Place this file on your Namecheap hosting and configure GitHub webhook to call it
 */

// Security: Set a secret token (change this!)
$secret = 'your_webhook_secret_here';

// Get the GitHub payload
$payload = file_get_contents('php://input');
$headers = getallheaders();

// Verify the webhook is from GitHub
if (!isset($headers['X-Hub-Signature-256'])) {
    http_response_code(403);
    die('Forbidden: Missing signature');
}

$signature = $headers['X-Hub-Signature-256'];
$expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (!hash_equals($signature, $expected)) {
    http_response_code(403);
    die('Forbidden: Invalid signature');
}

// Parse the payload
$data = json_decode($payload, true);

// Only deploy on push to master branch
if ($data['ref'] !== 'refs/heads/master') {
    die('Not master branch, skipping deployment');
}

// Log the deployment
$log = date('Y-m-d H:i:s') . " - Deploying commit: " . $data['head_commit']['id'] . "\n";
file_put_contents('deploy.log', $log, FILE_APPEND);

// Change to your website directory
chdir('/home/beelkstc/tc/');

// Execute deployment commands
$commands = [
    'git pull origin master 2>&1',
    'composer install --no-dev --optimize-autoloader 2>&1',
    'php artisan config:cache 2>&1',
    'php artisan route:cache 2>&1',
    'php artisan view:cache 2>&1',
    'php artisan statamic:stache:clear 2>&1',
    'php artisan statamic:stache:warm 2>&1',
    'chmod -R 755 storage bootstrap/cache 2>&1'
];

$output = [];
foreach ($commands as $command) {
    exec($command, $cmdOutput);
    $output[] = "$ $command";
    $output = array_merge($output, $cmdOutput);
}

// Log the output
$logOutput = implode("\n", $output) . "\n" . str_repeat('-', 50) . "\n";
file_put_contents('deploy.log', $logOutput, FILE_APPEND);

// Respond to GitHub
http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Deployment completed',
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
