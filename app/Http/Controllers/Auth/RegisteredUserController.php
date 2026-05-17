<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
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
            'student_id' => ['required', 'string', 'max:50', 'unique:users,student_id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'student_id' => $request->student_id,
        ]);

        // Force role to student and mark as pending approval
        $user->role = 'student';
        $user->is_approved = false;
        $user->save();

        // Notify all admins about the new registration
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'warning',
                'title' => 'New Student Registration',
                'message' => $user->name . ' (' . $user->student_id . ') has registered and is awaiting approval.',
                'action_url' => route('admin.users.index', ['role' => 'student', 'approval' => 'pending']),
                'priority' => 'high',
            ]);
        }

        // Do NOT auto-login — redirect to a waiting page
        return redirect()->route('login')->with('status', 'Your account has been created and is pending admin approval.');
    }
}
