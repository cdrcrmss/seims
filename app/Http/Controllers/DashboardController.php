<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Borrowing;
use App\Models\User;
use App\Models\Reservation;
use App\Models\MaintenanceRecord;
use App\Models\Notification;
use App\Services\PredictiveAnalyticsService;
use App\Services\BorrowingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $analytics;

    public function __construct(PredictiveAnalyticsService $analytics)
    {
        $this->analytics = $analytics;
    }

    /**
     * Display the appropriate dashboard based on user role.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Redirect to appropriate dashboard based on role
        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isStaff()) {
            return $this->staffDashboard();
        } else {
            return $this->studentDashboard();
        }
    }

    /**
     * Student Dashboard
     */
    private function studentDashboard()
    {
        try {
            $user = Auth::user();
            
            // Get available items (grouped by category)
            $availableItems = Item::where('available_stock', '>', 0)
                ->orderBy('name')
                ->get();
            
            $itemsByCategory = $availableItems->groupBy('category');
            
            // Get user's active borrowings
            $activeBorrowings = Borrowing::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'approved', 'issued'])
                ->with('item')
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Get user's borrowing history
            $borrowingHistory = Borrowing::where('user_id', $user->id)
                ->with('item')
                ->orderBy('created_at', 'desc')
                ->get();

            // User's reservations
            $myReservations = Reservation::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'approved'])
                ->with(['item', 'room'])
                ->orderBy('start_datetime')
                ->take(5)
                ->get();

            // Overdue count for student
            $overdueCount = Borrowing::where('user_id', $user->id)
                ->where('status', 'issued')
                ->where('expected_return_date', '<', now())
                ->count();

            // Unread notifications
            $notifications = Notification::where('user_id', $user->id)
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            return view('dashboard.student', compact(
                'availableItems',
                'itemsByCategory', 
                'activeBorrowings',
                'borrowingHistory',
                'myReservations',
                'overdueCount',
                'notifications'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Admin Dashboard
     */
    private function adminDashboard()
    {
        try {
            // Core stats
            $totalUsers = User::count();
            $totalItems = Item::count();
            $totalBorrowings = Borrowing::count();
            $pendingRequests = Borrowing::where('status', 'pending')->count();
            
            // Extended stats
            $activeBorrowings = Borrowing::whereIn('status', ['approved', 'issued'])->count();
            $overdueItems = Borrowing::where('status', 'issued')
                ->where('expected_return_date', '<', now())->count();
            $lowStockItems = Item::lowStock()->count();
            $maintenanceDue = MaintenanceRecord::where('status', 'scheduled')
                ->where('scheduled_date', '<=', now())->count();
            $pendingReservations = Reservation::where('status', 'pending')->count();

            // System health score
            $systemHealth = $this->analytics->getSystemHealthScore();

            // User breakdown
            $userBreakdown = [
                'admins' => User::where('role', 'admin')->count(),
                'staff' => User::where('role', 'staff')->count(),
                'students' => User::where('role', 'student')->count(),
            ];

            // Monthly borrowing trend (last 6 months)
            $monthlyTrend = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $monthlyTrend[] = [
                    'month' => $date->format('M'),
                    'count' => Borrowing::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)->count(),
                ];
            }

            // Recent activity (last 10 borrowings)
            $recentActivity = Borrowing::with(['user', 'item'])
                                     ->orderBy('created_at', 'desc')
                                     ->limit(10)
                                     ->get();

            // Critical items (high wear or overdue maintenance)
            $criticalItems = Item::where('wear_level', '>=', 60)
                ->orWhere(function($q) {
                    $q->whereNotNull('next_maintenance_date')
                      ->where('next_maintenance_date', '<', now());
                })
                ->orderBy('wear_level', 'desc')
                ->take(5)
                ->get();

            // Upcoming maintenance
            $upcomingMaintenance = MaintenanceRecord::with('item')
                ->where('status', 'scheduled')
                ->orderBy('scheduled_date')
                ->take(5)
                ->get();

            return view('dashboard.admin', compact(
                'totalUsers', 'totalItems', 'totalBorrowings', 'pendingRequests',
                'activeBorrowings', 'overdueItems', 'lowStockItems', 'maintenanceDue',
                'pendingReservations', 'systemHealth',
                'userBreakdown', 'monthlyTrend', 'recentActivity',
                'criticalItems', 'upcomingMaintenance'
            ));
        } catch (\Exception $e) {
            \Log::error('Admin Dashboard Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred loading the dashboard. Please try again.');
        }
    }

    /**
     * Staff Dashboard
     */
    private function staffDashboard()
    {
        try {
            $totalItems = Item::count();
            $lowStockItems = Item::lowStock()->count();
            $pendingRequests = Borrowing::where('status', 'pending')->count();
            $overdueItems = Borrowing::where('status', 'issued')
                                   ->where('expected_return_date', '<', now())
                                   ->count();

            // Extended stats
            $activeBorrowings = Borrowing::whereIn('status', ['approved', 'issued'])->count();
            $maintenanceDue = MaintenanceRecord::where('status', 'scheduled')
                ->where('scheduled_date', '<=', now())->count();
            $upcomingMaintenance = MaintenanceRecord::with('item')
                ->where('status', 'scheduled')
                ->orderBy('scheduled_date')
                ->take(5)
                ->get();
            $pendingReservations = Reservation::where('status', 'pending')->count();

            // System health
            $systemHealth = $this->analytics->getSystemHealthScore();

            // Overdue borrowings list
            $overdueBorrowings = Borrowing::with(['user', 'item'])
                ->where('status', 'issued')
                ->where('expected_return_date', '<', now())
                ->orderBy('expected_return_date')
                ->take(5)
                ->get();

            // Low stock items list
            $lowStockItemsList = Item::lowStock()
                ->orderBy('available_stock')
                ->take(5)
                ->get();

            // Get pending borrowings for quick action
            $pendingBorrowings = Borrowing::where('status', 'pending')
                                         ->with(['user', 'item'])
                                         ->orderBy('created_at', 'desc')
                                         ->take(5)
                                         ->get();

            return view('dashboard.staff', compact(
                'totalItems', 'lowStockItems', 'pendingRequests', 'overdueItems',
                'activeBorrowings', 'maintenanceDue', 'upcomingMaintenance',
                'pendingReservations', 'systemHealth', 'overdueBorrowings',
                'lowStockItemsList', 'pendingBorrowings'
            ));
        } catch (\Exception $e) {
            \Log::error('Staff Dashboard Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred loading the dashboard. Please try again.');
        }
    }

    /**
     * Request to borrow an item (for students)
     */
    public function requestBorrow(Request $request, BorrowingService $borrowingService)
    {
        // Only allow students to borrow
        if (!Auth::user()->isStudent()) {
            abort(403, 'Only students can borrow items.');
        }

        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'expected_return_date' => 'required|date|after:today',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $borrowingService->createBorrowRequest($request->only([
                'item_id', 'quantity', 'expected_return_date', 'notes'
            ]));

            return back()->with('success', 'Borrow request submitted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the user guide / manual.
     */
    public function userManual()
    {
        $path = resource_path('docs/user-manual.md');
        $markdown = file_exists($path) ? file_get_contents($path) : "# User Guide\n\nThe user manual is not available yet.";

        return view('user-manual', [
            'content' => Str::markdown($markdown),
        ]);
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'student_id' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'student_id' => $request->student_id,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided password does not match our records.']);
        }

        $user->update([
            'password' => $request->password,
        ]);

        return back()->with('success', 'Password updated successfully!');
    }
}