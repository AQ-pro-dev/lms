<?php
/**
 * Quick script to verify Vimeo environment variables
 * Run: php check-vimeo-env.php
 */

$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    echo "❌ .env file not found at: $envFile\n";
    exit(1);
}

echo "Checking .env file for Vimeo credentials...\n\n";

$content = file_get_contents($envFile);
$lines = explode("\n", $content);

$vimeoClient = null;
$vimeoSecret = null;
$vimeoAccess = null;

foreach ($lines as $line) {
    $line = trim($line);
    
    // Skip comments and empty lines
    if (empty($line) || strpos($line, '#') === 0) {
        continue;
    }
    
    // Check for VIMEO_CLIENT
    if (preg_match('/^VIMEO_CLIENT\s*=\s*(.+)$/i', $line, $matches)) {
        $vimeoClient = trim($matches[1], " \t\n\r\0\x0B\"'");
    }
    
    // Check for VIMEO_SECRET
    if (preg_match('/^VIMEO_SECRET\s*=\s*(.+)$/i', $line, $matches)) {
        $vimeoSecret = trim($matches[1], " \t\n\r\0\x0B\"'");
    }
    
    // Check for VIMEO_ACCESS
    if (preg_match('/^VIMEO_ACCESS\s*=\s*(.+)$/i', $line, $matches)) {
        $vimeoAccess = trim($matches[1], " \t\n\r\0\x0B\"'");
    }
}

echo "Found in .env file:\n";
echo "  VIMEO_CLIENT: " . ($vimeoClient ? "✅ SET (" . strlen($vimeoClient) . " chars)" : "❌ NOT SET") . "\n";
if ($vimeoClient) {
    echo "    First 10 chars: " . substr($vimeoClient, 0, 10) . "...\n";
}

echo "  VIMEO_SECRET: " . ($vimeoSecret ? "✅ SET (" . strlen($vimeoSecret) . " chars)" : "❌ NOT SET") . "\n";
if ($vimeoSecret) {
    echo "    First 10 chars: " . substr($vimeoSecret, 0, 10) . "...\n";
}

echo "  VIMEO_ACCESS: " . ($vimeoAccess ? "✅ SET (" . strlen($vimeoAccess) . " chars)" : "❌ NOT SET") . "\n";
if ($vimeoAccess) {
    echo "    First 20 chars: " . substr($vimeoAccess, 0, 20) . "...\n";
    echo "    Last 10 chars: ..." . substr($vimeoAccess, -10) . "\n";
    
    if (strlen($vimeoAccess) < 50) {
        echo "\n⚠️  WARNING: Access token is too short!\n";
        echo "   Vimeo access tokens should be 128+ characters.\n";
        echo "   Your token is only " . strlen($vimeoAccess) . " characters.\n";
        echo "   This suggests the token is invalid or incomplete.\n";
    } elseif (strlen($vimeoAccess) < 100) {
        echo "\n⚠️  WARNING: Access token seems short.\n";
        echo "   Vimeo access tokens are usually 128+ characters.\n";
    } else {
        echo "\n✅ Access token length looks good!\n";
    }
} else {
    echo "\n❌ VIMEO_ACCESS is not set in .env file!\n";
}

echo "\n---\n";
echo "Next steps:\n";
if (!$vimeoAccess || strlen($vimeoAccess) < 50) {
    echo "1. Go to https://developer.vimeo.com/\n";
    echo "2. Select your app\n";
    echo "3. Generate a NEW access token with 'video.upload' scope\n";
    echo "4. Copy the ENTIRE token (should be 128+ characters)\n";
    echo "5. Update VIMEO_ACCESS in your .env file\n";
    echo "6. Make sure there are NO quotes around the value\n";
    echo "7. Make sure there are NO spaces around the = sign\n";
    echo "8. Run: php artisan config:clear\n";
    echo "9. Run: php artisan vimeo:test\n";
} else {
    echo "✅ Your .env file looks correct!\n";
    echo "If you're still having issues, try:\n";
    echo "1. php artisan config:clear\n";
    echo "2. php artisan cache:clear\n";
    echo "3. Restart your web server\n";
    echo "4. php artisan vimeo:test\n";
}

