<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Allow lead scanner AJAX calls even if web session expires; identity is resolved via persistent cookie token.
        $middleware->validateCsrfTokens(except: [
            'lead/scan',
            'lead/scan/precheck',
            // Operator scanner supports long offline periods; token may expire before sync.
            'operator/scanning/check-user',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
