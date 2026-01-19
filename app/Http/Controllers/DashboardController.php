<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
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

            return view('dashboard.student', compact(
                'availableItems',
                'itemsByCategory', 
                'activeBorrowings',
                'borrowingHistory'
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
            $totalUsers = User::count();
            $totalItems = Item::count();
            $totalBorrowings = Borrowing::count();
            $pendingRequests = Borrowing::where('status', 'pending')->count();
            
            // Get recent activity (last 10 borrowings)
            $recentActivity = Borrowing::with(['user', 'item'])
                                     ->orderBy('created_at', 'desc')
                                     ->limit(10)
                                     ->get();

            return view('dashboard.admin', compact(
                'totalUsers', 'totalItems', 'totalBorrowings', 'pendingRequests', 'recentActivity'
            ));
        } catch (\Exception $e) {
            \Log::error('Admin Dashboard Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['error' => 'Dashboard error occurred.']);
        }
    }

    /**
     * Staff Dashboard
     */
    private function staffDashboard()
    {
        try {
            $totalItems = Item::count();
            $lowStockItems = Item::where('available_stock', '<', 5)->count();
            $pendingRequests = Borrowing::where('status', 'pending')->count();
            $overdueItems = Borrowing::where('status', 'issued')
                                   ->where('expected_return_date', '<', now())
                                   ->count();
            
            // Get pending borrowings for quick action
            $pendingBorrowings = Borrowing::where('status', 'pending')
                                         ->with(['user', 'item'])
                                         ->orderBy('created_at', 'desc')
                                         ->take(5)
                                         ->get();

            return view('dashboard.staff', compact(
                'totalItems',
                'lowStockItems',
                'pendingRequests',
                'overdueItems',
                'pendingBorrowings'
            ));
        } catch (\Exception $e) {
            \Log::error('Staff Dashboard Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['error' => 'Dashboard error occurred.']);
        }
    }

    /**
     * Request to borrow an item (for students)
     */
    public function requestBorrow(Request $request)
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

        $item = Item::findOrFail($request->item_id);

        // Check if enough stock is available
        if (!$item->isAvailable($request->quantity)) {
            return back()->withErrors(['quantity' => 'Insufficient stock available.']);
        }

        // Create borrowing request
        Borrowing::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'quantity' => $request->quantity,
            'status' => 'pending',
            'requested_date' => now(),
            'expected_return_date' => $request->expected_return_date,
            'notes' => $request->notes,
        ]);

        // Update available stock
        $item->decrement('available_stock', $request->quantity);

        return back()->with('success', 'Borrow request submitted successfully!');
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
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }
}