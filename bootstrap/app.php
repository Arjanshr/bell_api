<?php

use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\TrustProxies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Fortify\Fortify;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ Add TrustProxies to global middleware stack
        $middleware->append([
            TrustProxies::class,
        ]);

        // ✅ Alias middlewares (used in routes or groups)
        $middleware->alias([
            'is_admin' => IsAdmin::class,
            'ensure-user-active' => EnsureUserIsActive::class,
            'ensure-email-verified' => EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                if ($e instanceof \Laravel\Fortify\Exceptions\EmailVerificationRequiredException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your email address is not verified.',
                    ], 403);
                }

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }

            return null;
        });
    })
    ->create();
