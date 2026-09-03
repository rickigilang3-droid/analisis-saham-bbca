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
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        // 1. Aktifkan session untuk API (Sanctum)
        $middleware->statefulApi(); 

        // 2. Daftarin alias middleware admin lo
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminOnly::class,
        ]);

        // 3. KUNCI UTAMA: Bypass CSRF untuk route auth & API di serverless Vercel
        $middleware->validateCsrfTokens(except: [
            'login',
            'register',
            'logout',
            'api/*',
        ]);
    })->create();