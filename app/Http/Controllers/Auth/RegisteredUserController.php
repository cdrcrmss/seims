<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\NotifyAdminsOfRegistration;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'student_id' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'student_id' => $request->student_id,
        ]);

        // Force role to student and mark as pending approval
        $user->role = 'student';
        $user->is_approved = false;
        $user->save();

        // Fire Registered event to send verification email
        event(new Registered($user));

        // Queue admin notifications (non-blocking)
        NotifyAdminsOfRegistration::dispatch($user->id, $user->name, $user->student_id);

        // Do NOT auto-login — redirect to a waiting page
        return redirect()->route('login')->with('status', 'Your account has been created and is pending admin approval. You will be notified once approved.');
    }
}
