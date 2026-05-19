<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthSessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        protected AuthSessionService $authSessions
    ) {}

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Apply no-cache headers so login/logout pages are never served from bfcache.
     */
    protected function withNoCacheHeaders(HttpResponse $response): HttpResponse
    {
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return $this->withNoCacheHeaders(
            redirect()->intended(route('dashboard'))
        );
    }

    /**
     * Destroy an authenticated session (full server-side invalidation).
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->authSessions->destroy($request);

        $response = redirect()
            ->route('login', ['logged_out' => 1])
            ->with('status', 'You have been signed out securely.')
            ->withCookie($this->authSessions->forgetSessionCookie());

        return $this->withNoCacheHeaders($response);
    }
}
