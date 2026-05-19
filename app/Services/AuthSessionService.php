<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class AuthSessionService
{
    /**
     * Fully terminate the authenticated session (server-side and cookies).
     */
    public function destroy(Request $request): void
    {
        $userId = Auth::id();
        $sessionId = $request->session()->getId();

        Auth::guard('web')->logout();

        if ($userId !== null && $this->usesDatabaseSessions()) {
            DB::table($this->sessionTable())
                ->where('user_id', $userId)
                ->delete();
        }

        if ($sessionId && $this->usesDatabaseSessions()) {
            DB::table($this->sessionTable())
                ->where('id', $sessionId)
                ->delete();
        }

        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    /**
     * Queue forgetting of the session cookie on the response.
     */
    public function forgetSessionCookie(): \Symfony\Component\HttpFoundation\Cookie
    {
        return Cookie::forget(
            config('session.cookie'),
            config('session.path', '/'),
            config('session.domain')
        );
    }

    /**
     * Whether the current session id still exists in server storage.
     */
    public function sessionExistsOnServer(string $sessionId): bool
    {
        if ($sessionId === '') {
            return false;
        }

        if ($this->usesDatabaseSessions()) {
            return DB::table($this->sessionTable())->where('id', $sessionId)->exists();
        }

        if (config('session.driver') === 'file') {
            $path = config('session.files').DIRECTORY_SEPARATOR.$sessionId;

            return is_file($path);
        }

        return true;
    }

    public function usesDatabaseSessions(): bool
    {
        return config('session.driver') === 'database';
    }

    protected function sessionTable(): string
    {
        return config('session.table', 'sessions');
    }
}
