<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Middleware\LoggerMiddleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Enums\UserRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/auth.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'logger' => LoggerMiddleware::class,
            'admin' => AdminMiddleware::class,
            'super_admin' => SuperAdminMiddleware::class,
            
        ]);

        // Redirect guests (unauthenticated users) to the public home page
        $middleware->redirectGuestsTo(function (Request $request) {
            return route('home');
        });

        // Redirect authenticated users away from guest routes (like login)
        $middleware->redirectUsersTo(function (Request $request) {
            $user = Auth::user();
            
            if ($user && $user->role === UserRole::Logger) {
                return route('logger_dashboard');
            }
            
            if ($user && in_array($user->role, [UserRole::Admin, UserRole::SuperAdmin])) {
                return route('admin_dashboard');
            }
            
            // Fallback (shouldn't reach here if user is authenticated)
            return route('admin_dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
