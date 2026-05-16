<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PasswordResetLinkController extends Controller
{
    /**
     * Show instructions to contact the administrator for a password reset.
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Email-based reset is disabled; students and staff must contact an admin.
     */
    public function store(Request $request)
    {
        return redirect()->route('password.request');
    }
}
