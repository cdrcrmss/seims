<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the student dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get available items
        $availableItems = Item::where('available_stock', '>', 0)
                             ->orderBy('category')
                             ->orderBy('name')
                             ->get();

        // Get user's active borrowings
        $activeBorrowings = $user->activeBorrowings()
                                ->with('item')
                                ->orderBy('created_at', 'desc')
                                ->get();

        // Get borrowing history
        $borrowingHistory = $user->borrowings()
                                ->with('item')
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();

        // Group items by category
        $itemsByCategory = $availableItems->groupBy('category');

        return view('dashboard.student', compact(
            'availableItems',
            'activeBorrowings', 
            'borrowingHistory',
            'itemsByCategory'
        ));
    }

    /**
     * Request to borrow an item
     */
    public function requestBorrow(Request $request)
    {
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
}