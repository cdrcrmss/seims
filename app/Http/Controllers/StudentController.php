<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check if user is a student
     */
    private function ensureStudent()
    {
        if (Auth::user()->role !== 'student') {
            abort(403, 'Access denied. Students only.');
        }
    }

    public function dashboard()
    {
        $this->ensureStudent();
        
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
    }

    public function borrowItem(Request $request)
    {
        $this->ensureStudent();
        
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'expected_return_date' => 'required|date|after:today',
            'notes' => 'nullable|string|max:500'
        ]);

        $item = Item::findOrFail($request->item_id);
        
        // Check if item is available and has enough stock
        if ($item->available_stock < $request->quantity) {
            return back()->with('error', 'Not enough stock available for this item.');
        }

        // Check if user has any pending requests for this item
        $existingRequest = Borrowing::where('user_id', Auth::id())
            ->where('item_id', $item->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingRequest) {
            return back()->with('error', 'You already have a pending request for this item.');
        }

        // Create borrowing request
        Borrowing::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'quantity' => $request->quantity,
            'expected_return_date' => $request->expected_return_date,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Borrowing request submitted successfully! Please wait for approval.');
    }

    public function borrowings()
    {
        $this->ensureStudent();
        
        $borrowings = Borrowing::where('user_id', Auth::id())
            ->with('item')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.borrowings.index', compact('borrowings'));
    }

    public function cancelRequest($id)
    {
        $this->ensureStudent();
        
        $borrowing = Borrowing::where('user_id', Auth::id())
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        $borrowing->delete();

        return back()->with('success', 'Borrowing request cancelled successfully.');
    }
}