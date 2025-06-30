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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            // If you are building an SPA using Sanctum's CSRF protection:
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            // otherwise, for pure API token auth, this might not be strictly necessary here.
        ]);

        // Register 'auth:sanctum' as a route middleware alias
        $middleware->alias([
            'auth:sanctum' => \Laravel\Sanctum\Http\Middleware\AuthenticateWithSanctum::class, // Add this line
        ]);

        // You can also add global API middleware here if needed
        // $middleware->append([
        //     // \App\Http\Middleware\FooMiddleware::class,
        // ]);
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
