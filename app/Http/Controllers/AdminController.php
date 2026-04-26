<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Item;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * User Management
     */
    public function users(Request $request)
    {
        $search = $request->get('search');
        $role = $request->get('role');
        $approval = $request->get('approval');
        
        // Get counts for each role
        $pendingApprovalCount = User::where('is_approved', false)->count();
        $roleCounts = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'staff' => User::where('role', 'staff')->count(),
            'student' => User::where('role', 'student')->count(),
            'pending' => $pendingApprovalCount,
        ];

        $users = User::query()
            ->when($search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($role, function($query, $role) {
                return $query->where('role', $role);
            })
            ->when($approval === 'pending', function($query) {
                return $query->where('is_approved', false);
            })
            ->orderByRaw('is_approved ASC') // Show pending first
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.index', compact('users', 'search', 'role', 'approval', 'roleCounts'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,staff,faculty,student',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        // Set role explicitly (not mass-assignable for security)
        $user->role = $request->role;
        $user->save();

        return redirect()->route('admin.users.index')
                        ->with('success', 'User created successfully!');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string', 
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => 'required|in:admin,staff,faculty,student',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Prevent the last admin from downgrading their own role
        if ($user->id === auth()->id() && $user->role === 'admin' && $request->role !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['role' => 'Cannot change your role. You are the last admin.'])->withInput();
            }
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Set role explicitly (not mass-assignable for security)
        $user->role = $request->role;
        $user->save();

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('admin.users.index')
                        ->with('success', 'User updated successfully!');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $user->delete();
        
        return redirect()->route('admin.users.index')
                        ->with('success', 'User deleted successfully!');
    }

    /**
     * Approve a pending student account.
     */
    public function approveUser(User $user)
    {
        $user->update(['is_approved' => true]);

        // Notify the student
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'type' => 'success',
            'title' => 'Account Approved',
            'message' => 'Your account has been approved by an administrator. You can now log in and use SEIMS.',
            'action_url' => route('dashboard'),
            'priority' => 'high',
        ]);

        return back()->with('success', $user->name . '\'s account has been approved.');
    }

    /**
     * Reject (delete) a pending student account.
     */
    public function rejectUser(User $user)
    {
        if ($user->is_approved) {
            return back()->withErrors(['error' => 'This account is already approved.']);
        }

        $name = $user->name;
        $user->forceDelete(); // Permanently remove rejected registrations

        return back()->with('success', $name . '\'s registration has been rejected and removed.');
    }

    /**
     * System Reports
     */
    public function reports()
    {
        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();
        
        $totalItems = Item::count();
        $availableItems = Item::where('available_stock', '>', 0)->count();
        
        $activeBorrowings = Borrowing::whereIn('status', ['approved', 'issued'])->count();
        $pendingRequests = Borrowing::where('status', 'pending')->count();
        $overdueItems = Borrowing::where('status', 'issued')
                                ->where('expected_return_date', '<', now())
                                ->count();

        // Items by category for distribution chart
        $itemsByCategory = Item::selectRaw('category, COUNT(*) as count')
                              ->groupBy('category')
                              ->pluck('count', 'category')
                              ->toArray();

        // Recent activity from audit logs
        $recentActivity = \App\Models\AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'action' => $log->action,
                    'user' => $log->user ? $log->user->name : 'System',
                    'time' => $log->created_at->diffForHumans(),
                ];
            })
            ->toArray();

        // Top borrowers
        $topBorrowers = User::withCount(['borrowings' => function($query) {
                               $query->whereMonth('created_at', now()->month);
                           }])
                           ->whereHas('borrowings', function($query) {
                               $query->whereMonth('created_at', now()->month);
                           })
                           ->orderBy('borrowings_count', 'desc')
                           ->limit(5)
                           ->get()
                           ->map(function($user) {
                               return [
                                   'name' => $user->name,
                                   'count' => $user->borrowings_count,
                                   'role' => ucfirst($user->role)
                               ];
                           })
                           ->toArray();

        return view('admin.reports', compact(
            'totalUsers', 'newUsersThisMonth', 'totalItems', 'availableItems',
            'activeBorrowings', 'pendingRequests', 'overdueItems',
            'itemsByCategory', 'recentActivity', 'topBorrowers'
        ));
    }

    /**
     * System Settings
     */
    public function settings()
    {
        $settings = self::loadSettings();
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Load settings from the JSON file, with sensible defaults.
     */
    public static function loadSettings(): array
    {
        return Cache::remember('system_settings', 3600, function () {
            $defaults = [
                'system_name' => config('app.name', 'SEIMS'),
                'max_borrow_days' => 7,
                'max_items_per_user' => 5,
            ];

            if (\Illuminate\Support\Facades\Storage::disk('local')->exists('settings.json')) {
                $stored = json_decode(
                    \Illuminate\Support\Facades\Storage::disk('local')->get('settings.json'),
                    true
                );
                if (is_array($stored)) {
                    return array_merge($defaults, $stored);
                }
            }

            return $defaults;
        });
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'system_name' => 'required|string|max:255',
            'max_borrow_days' => 'required|integer|min:1|max:365',
            'max_items_per_user' => 'required|integer|min:1|max:100',
        ]);

        // Persist settings to a JSON file in storage
        $settings = [
            'system_name' => $request->system_name,
            'max_borrow_days' => (int) $request->max_borrow_days,
            'max_items_per_user' => (int) $request->max_items_per_user,
            'updated_at' => now()->toIso8601String(),
            'updated_by' => auth()->id(),
        ];

        \Illuminate\Support\Facades\Storage::disk('local')->put(
            'settings.json',
            json_encode($settings, JSON_PRETTY_PRINT)
        );

        Cache::forget('system_settings');

        return back()->with('success', 'Settings updated successfully!');
    }

    /**
     * Borrowing Management
     */
    public function borrowings(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');

        $borrowings = Borrowing::with(['user', 'item'])
            ->when($status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->when($search, function($query, $search) {
                return $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('item', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.borrowings.index', compact('borrowings', 'status', 'search'));
    }
}