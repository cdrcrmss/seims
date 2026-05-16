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

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }
}
