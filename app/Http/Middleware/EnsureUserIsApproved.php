<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    /**
     * Block unapproved students (self-registration requires admin approval).
     * Staff accounts are created by admin with is_approved=true and are not gated here.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->is_approved && $user->isStudent()) {
            if ($request->routeIs('logout') || $request->routeIs('account.pending')) {
                return $next($request);
            }

            return redirect()->route('account.pending');
        }

        return $next($request);
    }
}
