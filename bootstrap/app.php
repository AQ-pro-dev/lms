<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'checkrole' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle exceptions for Livewire requests to ensure JSON responses
        $exceptions->render(function (\Throwable $e, $request) {
            // For Livewire requests, ensure we return JSON even if there's an error
            if ($request->header('X-Livewire') || $request->wantsJson()) {
                // Clean any output that might have been generated
                if (ob_get_level() > 0) {
                    ob_clean();
                }
            }
        });
    })->create();
