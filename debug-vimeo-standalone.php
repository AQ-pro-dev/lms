<?php

require __DIR__ . '/vendor/autoload.php';

use Vimeo\Vimeo;

$logFile = __DIR__ . '/debug_output.txt';
file_put_contents($logFile, "Debugging Vimeo Headers (Standalone - File Log)...\n\n");

function logMsg($msg) {
    global $logFile;
    file_put_contents($logFile, $msg, FILE_APPEND);
}

// Load .env manually
$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    logMsg(".env not found\n");
    exit(1);
}
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $env[trim($key)] = trim($value);
    }
}

$clientId = $env['VIMEO_CLIENT'] ?? '';
$clientSecret = $env['VIMEO_SECRET'] ?? '';
$accessToken = $env['VIMEO_ACCESS'] ?? '';

logMsg("Client ID: " . substr($clientId, 0, 5) . "...\n");
logMsg("Access Token: " . substr($accessToken, 0, 5) . "...\n\n");

if (empty($clientId) || empty($accessToken)) {
    logMsg("Credentials missing\n");
    exit(1);
}

$lib = new Vimeo($clientId, $clientSecret, $accessToken);

$headers = []; // Use default headers (libraries 3.4 header)

logMsg("Testing Parameter Variations with Default Headers...\n");

// Test 1: Legacy (type=upload)
logMsg("Test 1: Legacy Parameters (type=upload)\n");
try {
    $response = $lib->request('/me/videos', ['type' => 'upload', 'upgrade_to_1080' => false], 'POST', true, $headers);
    if (isset($response['body']['upload']['upload_link'])) {
        logMsg("✅ Legacy: SUCCESS\n");
    } else {
        $error = $response['body']['error'] ?? 'Unknown';
        $devMsg = $response['body']['developer_message'] ?? '';
        logMsg("❌ Legacy: FAILED: $error\n");
        if ($devMsg) logMsg("   DevMsg: $devMsg\n");
    }
} catch (\Exception $e) {
    logMsg("❌ Legacy: EXCEPTION: " . $e->getMessage() . "\n");
}
logMsg("----------------------------------------\n");

// Test 2: Modern (upload.approach=tus)
logMsg("Test 2: Modern Parameters (upload.approach=tus)\n");
try {
    // Need a valid size, just fake one
    $params = [
        'upload' => [
            'approach' => 'tus',
            'size' => 1024
        ],
        'name' => 'Test Video'
    ];
    
    $response = $lib->request('/me/videos', $params, 'POST', true, $headers);
    if (isset($response['body']['upload']['upload_link'])) {
        logMsg("✅ Modern: SUCCESS\n");
    } else {
        $error = $response['body']['error'] ?? 'Unknown';
        $devMsg = $response['body']['developer_message'] ?? '';
        logMsg("❌ Modern: FAILED: $error\n");
        if ($devMsg) logMsg("   DevMsg: $devMsg\n");
    }
} catch (\Exception $e) {
    logMsg("❌ Modern: EXCEPTION: " . $e->getMessage() . "\n");
}
logMsg("----------------------------------------\n");
