<?php

namespace App\Http\Middleware;

use App\Services\AuthSessionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureServerSessionIsValid
{
    public function __construct(
        protected AuthSessionService $authSessions
    ) {}

    /**
     * Reject requests when the session cookie no longer maps to a valid server session.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('logout')) {
            return $next($request);
        }

        if (! Auth::check()) {
            return $next($request);
        }

        $sessionId = $request->session()->getId();

        if ($sessionId === '' || ! $this->authSessions->sessionExistsOnServer($sessionId)) {
            $this->authSessions->destroy($request);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your session has expired. Please sign in again.',
                ], 401);
            }

            return redirect()
                ->route('login')
                ->with('status', 'Your session has expired. Please sign in again.');
        }

        return $next($request);
    }
}
