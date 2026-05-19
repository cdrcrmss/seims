<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequestsException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'staff_or_admin' => \App\Http\Middleware\StaffOrAdminMiddleware::class,
            'approved' => \App\Http\Middleware\EnsureUserIsApproved::class,
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'no.cache' => \App\Http\Middleware\PreventBrowserCache::class,
            'session.valid' => \App\Http\Middleware\EnsureServerSessionIsValid::class,
        ]);

        // Harden all authenticated routes: no browser cache + server session validation
        $middleware->appendToGroup('auth', [
            \App\Http\Middleware\PreventBrowserCache::class,
            \App\Http\Middleware\EnsureServerSessionIsValid::class,
        ]);

        // Register AuditLog middleware globally for web requests
        $middleware->web(append: [
            \App\Http\Middleware\AuditLogMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            if ($request->is('login') && $request->isMethod('POST')) {
                $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;
                $minutes = max(1, (int) ceil((int) $retryAfter / 60));

                return redirect()
                    ->route('login')
                    ->withInput($request->only('email'))
                    ->withErrors([
                        'email' => "Too many sign-in attempts. Please wait {$minutes} minute(s) and try again.",
                    ]);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Too many requests. Please slow down and try again.',
                ], Response::HTTP_TOO_MANY_REQUESTS, $e->getHeaders());
            }

            return null;
        });
    })->create();
