<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Password resets are handled by a system administrator for all roles.
     */
    public function create(): View
    {
        return view('auth.forgot-password-contact-admin');
    }
}
