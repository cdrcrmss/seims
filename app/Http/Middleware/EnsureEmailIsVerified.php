<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     * Skip email verification for admin and staff users.
     */
    public function handle(Request $request, Closure $next, ?string $redirectToRoute = null): Response
    {
        $user = $request->user();

        // Skip email verification for admin and staff users
        if ($user && in_array($user->role, ['admin', 'staff'])) {
            return $next($request);
        }

        if (! $user || ! $user->hasVerifiedEmail()) {
            return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : redirect()->route($redirectToRoute ?: 'verification.notice');
        }

        return $next($request);
    }
}
