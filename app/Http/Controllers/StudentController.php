<?php

namespace App\Http\Controllers;

use App\Http\Requests\BorrowItemRequest;
use App\Models\Item;
use App\Models\Borrowing;
use App\Http\Controllers\AdminController;
use App\Services\BorrowingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    protected BorrowingService $borrowingService;

    public function __construct(BorrowingService $borrowingService)
    {
        $this->middleware('auth');
        $this->borrowingService = $borrowingService;
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
        
        // Get available items with pagination
        $availableItems = Item::borrowable()
            ->orderBy('name')
            ->paginate(20);
        
        $itemsByCategory = $availableItems->getCollection()->groupBy('category');
        
        // Get user's active borrowings
        $activeBorrowings = Borrowing::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved', 'issued'])
            ->with('item')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get user's borrowing history (paginated)
        $borrowingHistory = Borrowing::where('user_id', $user->id)
            ->with('item')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'history_page');
        
        return view('dashboard.student', compact(
            'availableItems',
            'itemsByCategory',
            'activeBorrowings',
            'borrowingHistory'
        ));
    }

    /**
     * Show the borrowing form page with available items.
     */
    public function borrowForm(Request $request)
    {
        $this->ensureStudent();

        $user = Auth::user();
        $settings = AdminController::loadSettings();
        $maxDays = max(1, (int) ($settings['max_borrow_days'] ?? 7));
        $maxItems = max(1, (int) ($settings['max_items_per_user'] ?? 5));

        $search = $request->get('search');
        $category = $request->get('category');

        $query = Item::borrowable();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        $availableItems = $query->orderBy('name')->paginate(12)->withQueryString();

        // Get all categories for filter
        $categories = Item::borrowable()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        // Active borrow count for the user
        $activeBorrowCount = Borrowing::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved', 'issued'])
            ->count();

        // Check for overdue items
        $hasOverdue = Borrowing::where('user_id', $user->id)
            ->where('status', 'issued')
            ->where('expected_return_date', '<', now())
            ->exists();

        // Pre-selected item (if coming from dashboard)
        $selectedItemId = $request->get('item_id');
        $selectedItem = $selectedItemId ? Item::find($selectedItemId) : null;

        return view('student.borrowings.borrow', compact(
            'availableItems',
            'categories',
            'activeBorrowCount',
            'maxDays',
            'maxItems',
            'hasOverdue',
            'selectedItem',
            'search',
            'category'
        ));
    }

    /**
     * Submit a borrow request (with strict validation & rate limiting).
     */
    public function borrowItem(BorrowItemRequest $request)
    {
        try {
            // Merge purpose into notes field for the service
            $data = $request->only(['item_id', 'quantity', 'expected_return_date']);
            $purpose = $request->input('purpose');
            $notes = $request->input('notes');
            $data['notes'] = "Purpose: {$purpose}" . ($notes ? "\nAdditional Notes: {$notes}" : '');

            $this->borrowingService->createBorrowRequest($data);

            return redirect()
                ->route('student.borrowings.index')
                ->with('success', 'Borrowing request submitted successfully! Please wait for staff approval.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function borrowings(Request $request)
    {
        $this->ensureStudent();
        
        $status = $request->get('status');

        $borrowings = Borrowing::where('user_id', Auth::id())
            ->with(['item', 'approver', 'issuer', 'rejector', 'returnedToUser'])
            ->when($status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.borrowings.index', compact('borrowings', 'status'));
    }

    public function cancelRequest($id)
    {
        $this->ensureStudent();
        
        $borrowing = Borrowing::where('user_id', Auth::id())
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        try {
            $this->borrowingService->cancelBorrowRequest($borrowing, 'Cancelled by student');
            return back()->with('success', 'Borrowing request cancelled successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel request: ' . $e->getMessage());
        }
    }

    /**
     * Request an extension for a borrowing.
     */
    public function requestExtension(Request $request, Borrowing $borrowing)
    {
        $this->ensureStudent();

        // Ensure the borrowing belongs to the student
        if ($borrowing->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'extension_date' => 'required|date|after:today',
            'extension_reason' => 'nullable|string|max:500',
        ]);

        try {
            $this->borrowingService->requestExtension(
                $borrowing,
                $request->extension_date,
                $request->extension_reason
            );
            return back()->with('success', 'Extension request submitted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * API endpoint for live item search
     */
    public function searchItems(Request $request)
    {
        $search = $request->get('q', '');

        $items = Item::borrowable()
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'category', 'laboratory', 'location', 'available_stock', 'asset_code', 'image_path']);

        return response()->json($items);
    }
}