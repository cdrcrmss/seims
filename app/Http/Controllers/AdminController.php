<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Item;
use App\Models\Borrowing;
use Illuminate\Http\Request;
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
        
        // Get counts for each role
        $roleCounts = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'staff' => User::where('role', 'staff')->count(),
            'student' => User::where('role', 'student')->count(),
        ];

        $users = User::query()
            ->when($search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($role, function($query, $role) {
                return $query->where('role', $role);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.index', compact('users', 'search', 'role', 'roleCounts'));
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
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,staff,student',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

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
            'role' => 'required|in:admin,staff,student',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

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

        // Recent activity (mock data - would be actual activity logs)
        $recentActivity = [
            ['action' => 'New user registered', 'user' => 'John Doe', 'time' => '2 hours ago'],
            ['action' => 'Item returned', 'user' => 'Jane Smith', 'time' => '3 hours ago'],
            ['action' => 'Borrowing request approved', 'user' => 'Mike Johnson', 'time' => '5 hours ago'],
            ['action' => 'New item added', 'user' => 'Staff User', 'time' => '1 day ago'],
        ];

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
        // For now, basic settings view
        return view('admin.settings.index');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'system_name' => 'required|string|max:255',
            'max_borrow_days' => 'required|integer|min:1|max:365',
            'max_items_per_user' => 'required|integer|min:1|max:100',
        ]);

        // Here you would typically save settings to database or config
        // For now, just return success
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