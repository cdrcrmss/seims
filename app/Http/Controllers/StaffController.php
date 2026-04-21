<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Borrowing;
use App\Models\Notification;
use App\Models\User;
use App\Mail\BorrowingApproved;
use App\Mail\BorrowingRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
        $status = $request->get('status'); // Item status: available, in_use, maintenance, damaged, lost, retired
        $stockFilter = $request->get('stock_filter'); // Stock level: in_stock, low_stock, out_of_stock
        
        // Stats counts (unfiltered)
        $totalItemsCount = Item::count();
        $availableItemsCount = Item::where('available_stock', '>', 0)->count();
        $outOfStockCount = Item::where('available_stock', 0)->count();
        $categoriesCount = Item::distinct('category')->count('category');

        $items = Item::query()
            ->when($search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                           ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($category, function($query, $category) {
                return $query->where('category', $category);
            })
            ->when($status, function($query, $status) {
                // Filter by item status field
                return $query->where('status', $status);
            })
            ->when($stockFilter, function($query, $stockFilter) {
                // Filter by stock level
                if ($stockFilter === 'in_stock') {
                    return $query->where('available_stock', '>', 5);
                } elseif ($stockFilter === 'low_stock') {
                    return $query->where('available_stock', '>', 0)->where('available_stock', '<=', 5);
                } elseif ($stockFilter === 'out_of_stock') {
                    return $query->where('available_stock', 0);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $categories = Item::distinct()->pluck('category')->filter();

        return view('staff.items.index', compact(
            'items', 'categories', 'search', 'category', 'status', 'stockFilter',
            'totalItemsCount', 'availableItemsCount', 'outOfStockCount', 'categoriesCount'
        ));
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
            'status' => 'nullable|string|in:available,in_use,maintenance,retired,damaged,lost',
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
            'status' => $request->status ?? $item->status,
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
     * Bulk import items from CSV.
     * Expected CSV columns: name, description, category, total_stock, available_stock
     */
    public function bulkImportItems(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            return back()->with('error', 'Could not read the CSV file.');
        }

        // Read header row
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return back()->with('error', 'CSV file is empty or invalid.');
        }

        // Normalize headers (trim whitespace, lowercase)
        $header = array_map(function ($h) {
            return strtolower(trim($h));
        }, $header);

        // Validate required columns
        $required = ['name', 'category', 'total_stock'];
        foreach ($required as $col) {
            if (!in_array($col, $header)) {
                fclose($handle);
                return back()->with('error', 'CSV is missing required column: ' . $col);
            }
        }

        $imported = 0;
        $errors = [];
        $row = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $row++;
            $rowData = array_combine($header, $data);

            // Basic validation
            if (empty($rowData['name']) || empty($rowData['category']) || empty($rowData['total_stock'])) {
                $errors[] = "Row {$row}: Missing required fields (name, category, total_stock).";
                continue;
            }

            $totalStock = (int) $rowData['total_stock'];
            $availableStock = isset($rowData['available_stock']) && $rowData['available_stock'] !== ''
                ? (int) $rowData['available_stock']
                : $totalStock;

            if ($totalStock < 1) {
                $errors[] = "Row {$row}: total_stock must be at least 1.";
                continue;
            }

            Item::create([
                'name' => trim($rowData['name']),
                'description' => trim($rowData['description'] ?? ''),
                'category' => trim($rowData['category']),
                'total_stock' => $totalStock,
                'available_stock' => min($availableStock, $totalStock),
            ]);

            $imported++;
        }

        fclose($handle);

        $message = "Successfully imported {$imported} item(s).";
        if (count($errors) > 0) {
            $message .= ' Errors: ' . implode(' | ', array_slice($errors, 0, 5));
        }

        return back()->with('success', $message);
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

        try {
            // Use transaction to ensure consistency
            \DB::transaction(function () use ($borrowing) {
                // Lock the item for update
                $item = Item::lockForUpdate()->findOrFail($borrowing->item_id);
                
                // Double-check stock availability (in case stock was already decremented at request time)
                // This is a safety check if the workflow changes
                if ($item->available_stock < 0) {
                    throw new \Exception('Insufficient stock available. Stock may have been decremented at request time.');
                }

                $borrowing->update([
                    'status' => 'approved',
                    'approved_date' => now(),
                    'approved_by' => auth()->id(),
                ]);

                // Send DB notification to the student
                Notification::create([
                    'user_id' => $borrowing->user_id,
                    'type' => 'success',
                    'title' => 'Borrow Request Approved',
                    'message' => 'Your request to borrow "' . $borrowing->item->name . '" has been approved! Please proceed to collect the item.',
                    'action_url' => route('student.borrowings.index'),
                    'priority' => 'high',
                ]);

                // Send email notification
                if ($borrowing->user && $borrowing->user->email) {
                    Mail::to($borrowing->user->email)->send(new BorrowingApproved($borrowing));
                }
            });

            return back()->with('success', 'Borrowing request approved successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function rejectBorrowing(Request $request, Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending requests can be rejected.']);
        }

        try {
            // Use transaction to ensure consistency
            \DB::transaction(function () use ($request, $borrowing) {
                // Return stock to available (stock was decremented when request was created)
                $borrowing->item->increment('available_stock', $borrowing->quantity);

                $borrowing->update([
                    'status' => 'rejected',
                    'rejection_reason' => $request->rejection_reason ?? 'Request rejected by staff',
                    'rejected_by' => auth()->id(),
                    'rejected_date' => now(),
                ]);

                // Send DB notification to the student
                Notification::create([
                    'user_id' => $borrowing->user_id,
                    'type' => 'danger',
                    'title' => 'Borrow Request Rejected',
                    'message' => 'Your request to borrow "' . $borrowing->item->name . '" has been rejected. Reason: ' . ($request->rejection_reason ?? 'No reason provided.'),
                    'action_url' => route('student.borrowings.index'),
                    'priority' => 'normal',
                ]);

                // Send email notification
                if ($borrowing->user && $borrowing->user->email) {
                    Mail::to($borrowing->user->email)->send(new BorrowingRejected($borrowing));
                }
            });

            return back()->with('success', 'Borrowing request rejected.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function issueBorrowing(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'approved') {
            return back()->withErrors(['error' => 'Only approved requests can be issued.']);
        }

        try {
            // Use transaction to ensure consistency
            \DB::transaction(function () use ($borrowing) {
                // Lock the item for update
                $item = Item::lockForUpdate()->findOrFail($borrowing->item_id);
                
                // Safety check: verify stock is not negative (stock should have been decremented at request time)
                if ($item->available_stock < 0) {
                    throw new \Exception('Cannot issue: insufficient stock available.');
                }

                $borrowing->update([
                    'status' => 'issued',
                    'issued_date' => now(),
                    'issued_by' => auth()->id(),
                ]);
            });

            return back()->with('success', 'Item issued successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function returnBorrowing(Request $request, Borrowing $borrowing)
    {
        if ($borrowing->status !== 'issued') {
            return back()->withErrors(['error' => 'Only issued items can be returned.']);
        }

        $request->validate([
            'return_condition' => 'required|in:good,fair,needs_repair,damaged',
            'return_notes' => 'nullable|string|max:500',
        ]);

        try {
            \DB::transaction(function () use ($borrowing, $request) {
                // Lock the item for update to prevent race conditions
                $item = Item::lockForUpdate()->findOrFail($borrowing->item_id);

                // Return stock to available
                $item->increment('available_stock', $borrowing->quantity);

                // If condition is damaged or needs_repair, update item wear level
                if (in_array($request->return_condition, ['needs_repair', 'damaged'])) {
                    $wearIncrease = $request->return_condition === 'damaged' ? 30 : 15;
                    $item->wear_level = min(100, $item->wear_level + $wearIncrease);
                    
                    // If damaged, update item status
                    if ($request->return_condition === 'damaged') {
                        $item->status = 'damaged';
                    }
                    $item->save();
                }

                $borrowing->update([
                    'status' => 'returned',
                    'returned_date' => now(),
                    'returned_to' => auth()->id(),
                    'return_condition' => $request->return_condition,
                    'return_notes' => $request->return_notes,
                ]);
            });

            return back()->with('success', 'Item returned successfully! Condition: ' . ucfirst(str_replace('_', ' ', $request->return_condition)));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Return failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Approve a borrowing extension request.
     */
    public function approveExtension(Borrowing $borrowing)
    {
        if (!$borrowing->extension_requested || $borrowing->extension_status !== 'pending') {
            return back()->withErrors(['error' => 'No pending extension request.']);
        }

        $borrowing->update([
            'expected_return_date' => $borrowing->extension_date,
            'extension_status' => 'approved',
        ]);

        Notification::create([
            'user_id' => $borrowing->user_id,
            'type' => 'success',
            'title' => 'Extension Approved',
            'message' => 'Your extension request for "' . $borrowing->item->name . '" has been approved. New return date: ' . $borrowing->extension_date,
            'action_url' => route('student.borrowings.index'),
            'priority' => 'normal',
        ]);

        return back()->with('success', 'Extension approved. New return date: ' . $borrowing->extension_date);
    }

    /**
     * Reject a borrowing extension request.
     */
    public function rejectExtension(Borrowing $borrowing)
    {
        if (!$borrowing->extension_requested || $borrowing->extension_status !== 'pending') {
            return back()->withErrors(['error' => 'No pending extension request.']);
        }

        $borrowing->update([
            'extension_status' => 'rejected',
        ]);

        Notification::create([
            'user_id' => $borrowing->user_id,
            'type' => 'danger',
            'title' => 'Extension Rejected',
            'message' => 'Your extension request for "' . $borrowing->item->name . '" has been rejected. Please return the item by the original date.',
            'action_url' => route('student.borrowings.index'),
            'priority' => 'normal',
        ]);

        return back()->with('success', 'Extension request rejected.');
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

        // Recent activity from audit logs
        $recentActivity = \App\Models\AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'type' => strtolower($log->action),
                    'action' => $log->description ?? $log->action,
                    'user' => $log->user ? $log->user->name : 'System',
                    'item' => $log->new_values['name'] ?? 'N/A',
                    'time' => $log->created_at->diffForHumans(),
                ];
            })
            ->toArray();

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
            'completion_rate' => Borrowing::whereMonth('updated_at', now()->month)
                                            ->whereIn('status', ['returned', 'rejected', 'cancelled'])->count() > 0
                ? round((Borrowing::whereMonth('updated_at', now()->month)->where('status', 'returned')->count() /
                    Borrowing::whereMonth('updated_at', now()->month)->whereIn('status', ['returned', 'rejected', 'cancelled'])->count()) * 100)
                : 0,
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