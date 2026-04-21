<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated Use StaffOrAdminMiddleware instead. This class is not registered in bootstrap/app.php.
 * Kept for reference only - safe to delete.
 */
class EnsureUserIsStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || (!auth()->user()->isStaff() && !auth()->user()->isAdmin())) {
            abort(403, 'Access denied. Staff role required.');
        }

        return $next($request);
    }
}