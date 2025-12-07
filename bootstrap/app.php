<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\LoggerMiddleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'logger' => LoggerMiddleware::class,
            'admin' => AdminMiddleware::class,
            'super_admin' => SuperAdminMiddleware::class,
            
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
