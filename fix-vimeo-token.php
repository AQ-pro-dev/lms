<?php
/**
 * Interactive script to help fix Vimeo access token
 * Run: php fix-vimeo-token.php
 */

echo "========================================\n";
echo "Vimeo Access Token Fixer\n";
echo "========================================\n\n";

$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    echo "❌ .env file not found at: $envFile\n";
    exit(1);
}

// Read current .env
$content = file_get_contents($envFile);
$lines = explode("\n", $content);

$vimeoAccessLine = null;
$vimeoAccessIndex = -1;

// Find the VIMEO_ACCESS line
foreach ($lines as $index => $line) {
    if (preg_match('/^VIMEO_ACCESS\s*=/i', $line)) {
        $vimeoAccessLine = $line;
        $vimeoAccessIndex = $index;
        break;
    }
}

if ($vimeoAccessIndex === -1) {
    echo "❌ VIMEO_ACCESS not found in .env file!\n";
    echo "Please add: VIMEO_ACCESS=your_token_here\n";
    exit(1);
}

// Extract current token
preg_match('/^VIMEO_ACCESS\s*=\s*(.+)$/i', $vimeoAccessLine, $matches);
$currentToken = isset($matches[1]) ? trim($matches[1], " \t\n\r\0\x0B\"'") : '';

echo "Current VIMEO_ACCESS token:\n";
echo "  Length: " . strlen($currentToken) . " characters\n";
if ($currentToken) {
    echo "  First 20 chars: " . substr($currentToken, 0, 20) . "...\n";
    echo "  Last 10 chars: ..." . substr($currentToken, -10) . "\n";
}

echo "\n";

if (strlen($currentToken) < 50) {
    echo "❌ PROBLEM DETECTED: Your access token is too short!\n";
    echo "   Current length: " . strlen($currentToken) . " characters\n";
    echo "   Required length: 128+ characters\n\n";
    
    echo "This is why uploads are failing. The token is invalid.\n\n";
    
    echo "========================================\n";
    echo "HOW TO FIX:\n";
    echo "========================================\n\n";
    
    echo "1. Go to: https://developer.vimeo.com/\n";
    echo "2. Log in and select your app\n";
    echo "3. Go to 'Authentication' or 'Access Tokens'\n";
    echo "4. Click 'Generate New Token' or 'Generate Access Token'\n";
    echo "5. SELECT THESE SCOPES (checkboxes):\n";
    echo "   ✅ video.upload (REQUIRED!)\n";
    echo "   ✅ video.edit\n";
    echo "   ✅ public\n";
    echo "   ✅ private (optional)\n";
    echo "6. Click 'Generate'\n";
    echo "7. COPY THE ENTIRE TOKEN (it will be 128+ characters)\n";
    echo "   - It might start with 'v1.' or similar\n";
    echo "   - Copy the COMPLETE string\n";
    echo "   - It should be very long (128+ characters)\n\n";
    
    echo "8. Update your .env file:\n";
    echo "   Find this line:\n";
    echo "   " . trim($vimeoAccessLine) . "\n\n";
    echo "   Replace it with:\n";
    echo "   VIMEO_ACCESS=your_complete_long_token_here\n\n";
    echo "   IMPORTANT:\n";
    echo "   - NO quotes around the token\n";
    echo "   - NO spaces around the = sign\n";
    echo "   - Paste the COMPLETE token (all 128+ characters)\n\n";
    
    echo "9. After updating, run:\n";
    echo "   php artisan config:clear\n";
    echo "   php artisan cache:clear\n";
    echo "   php check-vimeo-env.php\n";
    echo "   php artisan vimeo:test\n\n";
    
    echo "========================================\n";
    echo "VERIFICATION:\n";
    echo "========================================\n";
    echo "After updating, the token should show 128+ characters.\n";
    echo "If it's still short, you didn't copy the complete token.\n\n";
    
} else {
    echo "✅ Token length looks OK (" . strlen($currentToken) . " chars)\n";
    echo "If you're still having issues, the token might:\n";
    echo "- Not have the 'video.upload' scope\n";
    echo "- Be from a different app than your Client ID/Secret\n";
    echo "- Be expired or invalid\n\n";
    echo "Try generating a NEW token with the 'video.upload' scope.\n";
}

echo "\nCurrent .env file location: $envFile\n";
echo "You can edit it with any text editor.\n";

