<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    /**
     * Handle an incoming request.
     *
     * Unapproved students are redirected to a pending-approval page.
     * Admins and staff always pass through.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->is_approved && $user->isStudent()) {
            // Allow the logout route so the user isn't stuck
            if ($request->routeIs('logout') || $request->routeIs('account.pending')) {
                return $next($request);
            }

            return redirect()->route('account.pending');
        }

        return $next($request);
    }
}
