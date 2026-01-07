<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Vimeo\Laravel\Facades\Vimeo;

echo "Debugging Vimeo Headers...\n\n";

$headersToTest = [
    'Default (Library)' => [],
    'No Space' => ['Accept' => 'application/vnd.vimeo.*+json;version=3.4'],
    'With Space' => ['Accept' => 'application/vnd.vimeo.*+json; version=3.4'],
    'Version 3.2' => ['Accept' => 'application/vnd.vimeo.*+json;version=3.2'],
    'Just 3.4' => ['Accept' => '3.4'], // As per error message text (unlikely but testing)
];

foreach ($headersToTest as $name => $headers) {
    echo "Testing: $name\n";
    try {
        // We use 'POST /me/videos' to test upload permissions as that's where the error occurred
        $response = Vimeo::request('/me/videos', ['type' => 'upload', 'upgrade_to_1080' => false], 'POST', true, $headers);
        
        if (isset($response['body']['upload']['upload_link'])) {
            echo "✅ SUCCESS\n";
        } else {
            echo "❌ FAILED: " . json_encode($response['body']) . "\n";
        }
    } catch (\Exception $e) {
        $msg = $e->getMessage();
        if ($e instanceof \Vimeo\Exceptions\VimeoRequestException) {
            $body = $e->getBody();
            if (isset($body['developer_message'])) {
                $msg = $body['developer_message'];
            } elseif (isset($body['error'])) {
                $msg = $body['error'];
            }
        }
        echo "❌ EXCEPTION: $msg\n";
    }
    echo "----------------------------------------\n";
}
