<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'staff_or_admin']);
    }

    /**
     * Item Management
     */
    public function items(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');
        $status = $request->get('status');
        
        $items = Item::query()
            ->when($search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                           ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($category, function($query, $category) {
                return $query->where('category', $category);
            })
            ->when($status, function($query, $status) {
                if ($status === 'in_stock') {
                    return $query->where('available_stock', '>', 5);
                } elseif ($status === 'low_stock') {
                    return $query->where('available_stock', '>', 0)->where('available_stock', '<=', 5);
                } elseif ($status === 'out_of_stock') {
                    return $query->where('available_stock', 0);
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $categories = Item::distinct()->pluck('category')->filter();

        return view('staff.items.index', compact('items', 'categories', 'search', 'category', 'status'));
    }

    public function createItem()
    {
        return view('staff.items.create');
    }

    public function storeItem(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'total_stock' => 'required|integer|min:1',
            'available_stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Default available_stock to total_stock if not provided
        $availableStock = $request->available_stock ?? $request->total_stock;

        // Validate that available stock doesn't exceed total stock
        if ($availableStock > $request->total_stock) {
            return back()->withErrors([
                'available_stock' => 'Available stock cannot exceed total stock.'
            ])->withInput();
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'public');
        }

        Item::create([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'total_stock' => $request->total_stock,
            'available_stock' => $availableStock,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('staff.items.index')
                        ->with('success', 'Item added successfully!');
    }

    public function editItem(Item $item)
    {
        return view('staff.items.edit', compact('item'));
    }

    public function updateItem(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'total_stock' => 'required|integer|min:1',
            'available_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Validate that available stock doesn't exceed total stock
        if ($request->available_stock > $request->total_stock) {
            return back()->withErrors([
                'available_stock' => 'Available stock cannot exceed total stock.'
            ])->withInput();
        }

        $imagePath = $item->image_path;
        if ($request->hasFile('image')) {
            // Delete old image
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('items', 'public');
        }

        $item->update([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'total_stock' => $request->total_stock,
            'available_stock' => $request->available_stock,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('staff.items.index')
                        ->with('success', 'Item updated successfully!');
    }

    public function deleteItem(Item $item)
    {
        // Check if item has active borrowings
        if ($item->borrowings()->whereIn('status', ['pending', 'approved', 'issued'])->exists()) {
            return back()->withErrors(['error' => 'Cannot delete item with active borrowings.']);
        }

        // Delete image
        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }

        $item->delete();
        
        return redirect()->route('staff.items.index')
                        ->with('success', 'Item deleted successfully!');
    }

    /**
     * Borrowing Requests Management
     */
    public function borrowings(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');

        // Get counts for each status
        $statusCounts = [
            'pending' => Borrowing::where('status', 'pending')->count(),
            'approved' => Borrowing::where('status', 'approved')->count(),
            'issued' => Borrowing::where('status', 'issued')->count(),
            'returned' => Borrowing::where('status', 'returned')->count(),
            'rejected' => Borrowing::where('status', 'rejected')->count(),
        ];

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

        return view('staff.borrowings.index', compact('borrowings', 'status', 'search', 'statusCounts'));
    }

    public function approveBorrowing(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending requests can be approved.']);
        }

        $borrowing->update([
            'status' => 'approved',
            'approved_date' => now(),
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Borrowing request approved successfully!');
    }

    public function rejectBorrowing(Request $request, Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending requests can be rejected.']);
        }

        // Return stock to available
        $borrowing->item->increment('available_stock', $borrowing->quantity);

        $borrowing->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason ?? 'Request rejected by staff',
            'rejected_by' => auth()->id(),
            'rejected_date' => now(),
        ]);

        return back()->with('success', 'Borrowing request rejected.');
    }

    public function issueBorrowing(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'approved') {
            return back()->withErrors(['error' => 'Only approved requests can be issued.']);
        }

        $borrowing->update([
            'status' => 'issued',
            'issued_date' => now(),
            'issued_by' => auth()->id(),
        ]);

        return back()->with('success', 'Item issued successfully!');
    }

    public function returnBorrowing(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'issued') {
            return back()->withErrors(['error' => 'Only issued items can be returned.']);
        }

        // Return stock to available
        $borrowing->item->increment('available_stock', $borrowing->quantity);

        $borrowing->update([
            'status' => 'returned',
            'returned_date' => now(),
            'returned_to' => auth()->id(),
        ]);

        return back()->with('success', 'Item returned successfully!');
    }

    /**
     * Reports
     */
    public function reports()
    {
        $totalItems = Item::count();
        $itemsAdded = Item::whereMonth('created_at', now()->month)->count();
        
        $activeBorrowings = Borrowing::whereIn('status', ['approved', 'issued'])->count();
        $borrowingsToday = Borrowing::whereDate('created_at', today())->count();
        
        $pendingRequests = Borrowing::where('status', 'pending')->count();
        $itemsDueSoon = Borrowing::where('status', 'issued')
                                ->where('expected_return_date', '<=', now()->addDays(7))
                                ->count();

        // Popular items with usage stats
        $popularItems = Item::withCount(['borrowings' => function($query) {
                               $query->where('created_at', '>=', now()->subMonths(1));
                           }])
                           ->whereHas('borrowings')
                           ->orderBy('borrowings_count', 'desc')
                           ->limit(10)
                           ->get()
                           ->map(function($item) {
                               return [
                                   'name' => $item->name,
                                   'category' => $item->category,
                                   'stock' => $item->available_stock . '/' . $item->total_stock,
                                   'borrow_count' => $item->borrowings_count,
                                   'status' => $item->available_stock > 0 ? 'available' : 'out_of_stock',
                                   'usage_rate' => $item->total_stock > 0 ? 
                                       round((($item->total_stock - $item->available_stock) / $item->total_stock) * 100) : 0
                               ];
                           })->toArray();

        // Recent activity (mock data - would be actual activity logs)
        $recentActivity = [
            ['type' => 'borrow', 'action' => 'Item borrowed', 'user' => 'John Doe', 'item' => 'Microscope', 'time' => '2 hours ago'],
            ['type' => 'return', 'action' => 'Item returned', 'user' => 'Jane Smith', 'item' => 'Calculator', 'time' => '3 hours ago'],
            ['type' => 'request', 'action' => 'New borrow request', 'user' => 'Mike Johnson', 'item' => 'Projector', 'time' => '5 hours ago'],
            ['type' => 'return', 'action' => 'Item returned late', 'user' => 'Sarah Wilson', 'item' => 'Laptop', 'time' => '1 day ago'],
        ];

        // Items due soon
        $itemsDue = Borrowing::with(['user', 'item'])
                            ->where('status', 'issued')
                            ->whereNotNull('expected_return_date')
                            ->where('expected_return_date', '<=', now()->addDays(7))
                            ->orderBy('expected_return_date')
                            ->limit(10)
                            ->get()
                            ->map(function($borrowing) {
                                $dueDate = \Carbon\Carbon::parse($borrowing->expected_return_date);
                                return [
                                    'item_name' => $borrowing->item->name,
                                    'borrower' => $borrowing->user->name,
                                    'due_date' => $dueDate->format('M d'),
                                    'days_until_due' => now()->diffInDays($dueDate, false)
                                ];
                            })->toArray();

        // Monthly stats
        $monthlyStats = [
            'completed_requests' => Borrowing::whereMonth('updated_at', now()->month)
                                            ->where('status', 'returned')->count(),
            'completion_rate' => 95, // Mock data
            'new_items' => $itemsAdded,
            'active_students' => User::where('role', 'student')
                                   ->whereHas('borrowings', function($query) {
                                       $query->whereMonth('created_at', now()->month);
                                   })->count()
        ];

        return view('staff.reports', compact(
            'totalItems', 'itemsAdded', 'activeBorrowings', 'borrowingsToday',
            'pendingRequests', 'itemsDueSoon', 'popularItems', 'recentActivity',
            'itemsDue', 'monthlyStats'
        ));
    }
}