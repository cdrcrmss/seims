<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\MailConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Step 1: enter account email to determine reset flow by role.
     */
    public function create(): View
    {
        return view('auth.forgot-password-identify');
    }

    /**
     * Step 2: route admins to email reset; staff/students to contact-admin page.
     */
    public function lookup(Request $request): View|RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'No account found with this email address.']);
        }

        if ($user->isAdmin()) {
            return view('auth.forgot-password', ['email' => $user->email]);
        }

        return view('auth.forgot-password-contact-admin');
    }

    /**
     * Send password reset link (admin accounts only).
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'No account found with this email address.']);
        }

        if (! $user->isAdmin()) {
            return redirect()->route('password.request')
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Password reset via email is only available for administrator accounts.']);
        }

        if (! MailConfig::canDeliver()) {
            if (app()->environment('local') && config('app.debug')) {
                return $this->issueDevResetLink($request, $user);
            }

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => $this->mailNotConfiguredMessage()]);
        }

        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'We could not send the reset email. Check your SMTP settings (host, port, username, app password) and try again.']);
        }

        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }

    /**
     * Local development: show reset URL when mail only logs to storage/logs.
     */
    protected function issueDevResetLink(Request $request, User $user): RedirectResponse
    {
        $token = Password::broker()->createToken($user);
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $request->email,
        ], false));

        return back()
            ->withInput($request->only('email'))
            ->with('dev_reset_url', $url)
            ->with('status', 'Mail is not configured (development mode). Use the reset link below.');
    }

    protected function mailNotConfiguredMessage(): string
    {
        if (filled(env('AWS_BUCKET')) || filled(env('LARAVEL_CLOUD'))) {
            return 'Email is not configured on this server. In Laravel Cloud → your environment → Variables, set MAIL_MAILER=smtp, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, and MAIL_FROM_ADDRESS (use a Gmail app password for Gmail).';
        }

        return 'Email is not configured on this server (MAIL_MAILER is set to log). Configure SMTP in your .env file to receive reset links. See .env.example for Gmail SMTP settings.';
    }
}
