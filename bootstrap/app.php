<?php

use App\Http\Middleware\HavePermission;
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
            'can' => HavePermission::class
        ]);

        if (env('APP_ENV') === 'production') {
            $middleware->trustHosts([env('APP_URL')])
                ->trustProxies('*');
        }
    })
    ->withEvents([
        __DIR__ . '/../app/Listeners'
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
