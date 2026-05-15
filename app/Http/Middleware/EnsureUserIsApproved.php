<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    /**
     * Block all unapproved accounts (students, staff, faculty).
     * Admins are always allowed through.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->is_approved && !$user->isAdmin()) {
            if ($request->routeIs('logout') || $request->routeIs('account.pending')) {
                return $next($request);
            }

            return redirect()->route('account.pending');
        }

        return $next($request);
    }
}
