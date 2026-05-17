<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Show forgot-password flow. If email is provided and belongs to student/staff, show contact-admin page.
     */
    public function create(Request $request)
    {
        $email = trim((string) $request->query('email', ''));

        if ($email !== '') {
            $user = User::where('email', $email)->first();

            if ($user && ! $user->isAdmin()) {
                return view('auth.forgot-password-contact-admin');
            }
        }

        return view('auth.forgot-password', [
            'email' => old('email', $email),
        ]);
    }

    /**
     * Route by account role: admin receives email reset link; student/staff see contact-admin instructions.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'We could not find an account with that email address.']);
        }

        if (! $user->isAdmin()) {
            return view('auth.forgot-password-contact-admin');
        }

        if (! $this->mailCanDeliver()) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Email is not configured on this server (MAIL_MAILER is set to log). '
                        . 'Configure SMTP in your .env file to receive reset links in Gmail. '
                        . 'See .env.example for Gmail SMTP settings.',
                ]);
        }

        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        }

        return back()->with('status', 'We have emailed your password reset link. Check your inbox and spam folder.');
    }

    /**
     * True when mail is configured to send off-server (not log/array drivers).
     */
    private function mailCanDeliver(): bool
    {
        return ! in_array(config('mail.default'), ['log', 'array'], true);
    }
}
