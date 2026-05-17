<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Item;
use App\Models\Borrowing;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

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
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('student_id', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($query, $role) {
                return $query->where('role', $role);
            })
            ->when($approval === 'pending', function ($query) {
                return $query->where('is_approved', false);
            })
            ->orderByRaw('is_approved ASC')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'role', 'approval', 'roleCounts'));
    }

    /**
     * JSON search suggestions for the user management autocomplete.
     */
    public function searchUsers(Request $request)
    {
        $search = trim($request->get('q', ''));
        if (strlen($search) < 2) {
            return response()->json(['users' => []]);
        }

        $role = $request->get('role');

        $users = User::query()
            ->when($role, fn ($q) => $q->where('role', $role))
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'email', 'role', 'student_id']);

        return response()->json([
            'users' => $users->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'detail' => collect([
                    ucfirst($u->role),
                    $u->student_id ? ($u->role === 'staff' ? 'Staff ID: ' : 'Student ID: ') . $u->student_id : null,
                    $u->email,
                ])->filter()->implode(' · '),
            ]),
        ]);
    }

    /**
     * Admin resets a student or staff member's password.
     */
    public function resetPassword(Request $request, User $user)
    {
        if ($user->isAdmin()) {
            return back()->withErrors(['error' => 'Administrator passwords cannot be reset from this screen.']);
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update(['password' => $request->password]);

        return redirect()->route('admin.users.index', $request->only(['search', 'role', 'approval']))
            ->with('success', "Password updated for {$user->name}. Share the new password with them securely.");
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
            'role' => 'required|in:staff,student',
            'student_id' => ['nullable', 'string', 'max:50', Rule::unique('users', 'student_id')],
        ]);

        if ($request->input('role') === 'admin') {
            return back()->withErrors(['role' => 'Cannot create administrator accounts. Only one system admin is allowed.'])->withInput();
        }

        if (in_array($request->role, ['student', 'staff'], true) && empty(trim($request->student_id ?? ''))) {
            $message = $request->role === 'staff'
                ? 'Staff / Employee ID is required for staff accounts.'
                : 'Student ID is required for student accounts.';
            return back()->withErrors(['student_id' => $message])->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'student_id' => trim($request->student_id ?? '') ?: null,
            'is_approved' => true,
        ]);
        // Set role explicitly (not mass-assignable for security)
        $user->role = $request->role;
        $user->save();

        return redirect()->route('admin.users.index')
                        ->with('success', ucfirst($request->role) . ' account created. They can sign in immediately with the credentials you provided.');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        if ($user->isAdmin()) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
                'password' => 'nullable|string|min:8|confirmed',
            ]);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => $request->password]);
            }

            return redirect()->route('admin.users.index')
                ->with('success', 'Administrator account updated successfully!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => 'required|in:staff,student',
            'student_id' => ['nullable', 'string', 'max:50', Rule::unique('users', 'student_id')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (in_array($request->role, ['student', 'staff'], true) && empty(trim($request->student_id ?? ''))) {
            $message = $request->role === 'staff'
                ? 'Staff / Employee ID is required for staff accounts.'
                : 'Student ID is required for student accounts.';
            return back()->withErrors(['student_id' => $message])->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'student_id' => trim($request->student_id ?? '') ?: null,
        ]);

        $user->role = $request->role;
        $user->save();

        if ($request->filled('password')) {
            $user->update(['password' => $request->password]);
        }

        return redirect()->route('admin.users.index')
                        ->with('success', 'User updated successfully!');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        if ($user->isAdmin()) {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['error' => 'Cannot delete the only administrator account.']);
            }
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
        $itemsByCategory = Item::query()
            ->selectRaw("COALESCE(NULLIF(TRIM(category), ''), 'Uncategorized') as category_label, COUNT(*) as count")
            ->groupBy('category_label')
            ->orderByDesc('count')
            ->pluck('count', 'category_label')
            ->toArray();

        // Recent activity: audit logs + recent borrowings (works even if queue worker is off)
        $recentActivity = collect();

        \App\Models\AuditLog::with('user')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->each(function ($log) use ($recentActivity) {
                $recentActivity->push([
                    'action' => $log->description ?: ucfirst($log->action).' ('.$log->module.')',
                    'user' => $log->user?->name ?? 'System',
                    'time' => $log->created_at->diffForHumans(),
                    'at' => $log->created_at,
                ]);
            });

        Borrowing::with(['user', 'item'])
            ->orderByDesc('updated_at')
            ->limit(12)
            ->get()
            ->each(function ($borrowing) use ($recentActivity) {
                $itemName = $borrowing->item?->name ?? 'an item';
                $statusLabel = match ($borrowing->status) {
                    'pending' => 'Requested borrow',
                    'approved' => 'Approved borrow',
                    'issued' => 'Issued',
                    'returned' => 'Returned',
                    'rejected' => 'Rejected borrow',
                    'cancelled' => 'Cancelled borrow',
                    default => ucfirst($borrowing->status).' borrow',
                };

                $recentActivity->push([
                    'action' => "{$statusLabel}: {$itemName}",
                    'user' => $borrowing->user?->name ?? 'Unknown user',
                    'time' => $borrowing->updated_at->diffForHumans(),
                    'at' => $borrowing->updated_at,
                ]);
            });

        $recentActivity = $recentActivity
            ->sortByDesc('at')
            ->take(10)
            ->map(fn ($row) => [
                'action' => $row['action'],
                'user' => $row['user'],
                'time' => $row['time'],
            ])
            ->values()
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

        // Borrowing trends for last 30 days
        $borrowingTrends = collect(range(29, 0))->map(function($daysAgo) {
            $date = now()->subDays($daysAgo)->toDateString();
            return [
                'date' => $date,
                'label' => now()->subDays($daysAgo)->format('M d'),
                'count' => Borrowing::whereDate('created_at', $date)->count(),
            ];
        })->values()->toArray();

        // Most borrowed items (last 30 days)
        $mostBorrowedItems = Borrowing::select('item_id', \DB::raw('COUNT(*) as borrow_count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('item_id')
            ->orderByDesc('borrow_count')
            ->limit(10)
            ->with('item')
            ->get()
            ->filter(fn($b) => $b->item !== null)
            ->map(fn($b) => [
                'name' => $b->item->name,
                'category' => $b->item->category,
                'count' => $b->borrow_count,
            ])
            ->values()
            ->toArray();

        return view('admin.reports', compact(
            'totalUsers', 'newUsersThisMonth', 'totalItems', 'availableItems',
            'activeBorrowings', 'pendingRequests', 'overdueItems',
            'itemsByCategory', 'recentActivity', 'topBorrowers', 'borrowingTrends', 'mostBorrowedItems'
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
                $settings = array_merge($defaults, $stored);
                $settings['max_borrow_days'] = max(1, (int) ($settings['max_borrow_days'] ?? 7));
                $settings['max_items_per_user'] = max(1, (int) ($settings['max_items_per_user'] ?? 5));

                return $settings;
            }
        }

        return $defaults;
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

        return back()->with('success', 'Settings updated successfully!');
    }

    /**
     * Borrowing Management
     */
    public function borrowings(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');
        $archived = $request->get('archived', '0');
        $active = $request->boolean('active');
        $overdue = $request->boolean('overdue');

        $borrowings = Borrowing::with(['user', 'item', 'approver', 'issuer', 'rejector', 'returnedToUser'])
            ->where('is_archived', $archived === '1')
            ->when($overdue, function ($query) {
                return $query->where('status', 'issued')
                    ->where('expected_return_date', '<', now());
            })
            ->when($active && ! $overdue, function ($query) {
                return $query->whereIn('status', ['approved', 'issued']);
            })
            ->when($status && ! $overdue && ! $active, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($search, function($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })->orWhereHas('item', function($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.borrowings.index', compact('borrowings', 'status', 'search', 'archived', 'active', 'overdue'));
    }

    public function archiveBorrowing(Borrowing $borrowing)
    {
        $borrowing->update(['is_archived' => true]);
        return back()->with('success', 'Borrowing record archived successfully.');
    }

    public function unarchiveBorrowing(Borrowing $borrowing)
    {
        $borrowing->update(['is_archived' => false]);
        return back()->with('success', 'Borrowing record restored from archive.');
    }
}