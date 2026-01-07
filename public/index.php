<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Start output buffering early to catch any warnings/errors
ob_start();

// Suppress proc_open warnings from sebastian/version (git command not found on Windows)
// This prevents HTML warnings from breaking Livewire JSON responses
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    // Always suppress proc_open warnings from sebastian/version
    if ($errno === E_WARNING && 
        (strpos($errstr, 'proc_open(): CreateProcess failed') !== false || 
         strpos($errstr, 'proc_open') !== false) &&
        strpos($errfile, 'sebastian/version') !== false) {
        return true; // Suppress this warning
    }
    // Suppress warnings/notices for Livewire/AJAX requests
    $isLivewireRequest = isset($_SERVER['HTTP_X_LIVEWIRE']) || 
                        (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    if ($isLivewireRequest && ($errno === E_WARNING || $errno === E_NOTICE)) {
        return true; // Suppress warnings/notices for Livewire requests
    }
    // Let other errors through
    return false;
}, E_WARNING | E_NOTICE);

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Clear any output that might have been generated during autoload
if (ob_get_level() > 0) {
    ob_clean();
}

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
