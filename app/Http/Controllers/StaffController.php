<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\Borrowing;
use App\Models\Notification;
use App\Models\User;
use App\Mail\BorrowingApproved;
use App\Mail\BorrowingRejected;
use App\Services\BorrowingService;
use App\Support\InventoryCodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    protected BorrowingService $borrowingService;

    public function __construct(BorrowingService $borrowingService)
    {
        $this->middleware(['auth', 'staff_or_admin']);
        $this->borrowingService = $borrowingService;
    }

    /**
     * Item Management
     */
    public function items(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');
        $laboratory = $request->get('laboratory');
        $status = $request->get('status'); // Item status: available, in_use, maintenance, damaged, lost, disposed
        $stockFilter = $request->get('stock_filter'); // Stock level: in_stock, low_stock, out_of_stock

        // Legacy links used status=out_of_stock; map to stock_filter
        if ($status === 'out_of_stock') {
            $stockFilter = $stockFilter ?: 'out_of_stock';
            $status = null;
        }
        
        // Stats counts (unfiltered)
        $totalItemsCount = Item::count();
        $availableItemsCount = Item::where('available_stock', '>', 0)->count();
        $outOfStockCount = Item::where('available_stock', 0)->count();
        $categoriesCount = Item::distinct('category')->count('category');
        $damagedCount = \App\Models\ItemUnit::where('status', 'damaged')->count();

        $items = Item::query()
            ->withCount(['units as damaged_units_count' => function($query) {
                $query->where('status', 'damaged');
            }])
            ->when($search, function ($query, $search) {
                $term = '%' . $search . '%';
                $startsWith = $search . '%';

                return $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                      ->orWhere('asset_code', 'like', $term);
                })->orderByRaw('CASE WHEN name LIKE ? THEN 0 ELSE 1 END', [$startsWith]);
            })
            ->when($category, function($query, $category) {
                return $query->where('category', $category);
            })
            ->when($laboratory, function($query, $laboratory) {
                return $query->where('laboratory', $laboratory);
            })
            ->when($status, function($query, $status) {
                if ($status === 'damaged') {
                    return $query->whereHas('units', function($q) {
                        $q->where('status', 'damaged');
                    });
                }
                if ($status === 'disposed') {
                    return $query->whereIn('status', ['disposed', 'retired']);
                }
                return $query->where('status', $status);
            })
            ->when($stockFilter, function ($query, $stockFilter) {
                if ($stockFilter === 'in_stock') {
                    return $query->whereColumn('available_stock', '>', 'low_stock_threshold');
                }

                if ($stockFilter === 'low_stock') {
                    return $query->lowStock();
                }

                if ($stockFilter === 'out_of_stock') {
                    return $query->outOfStock();
                }

                return $query;
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $categories = $this->getItemCategories();
        $laboratories = ['Alfresco', 'Kitchen', 'Food Lab', 'Hotel'];

        return view('staff.items.index', compact(
            'items', 'categories', 'laboratories', 'search', 'category', 'laboratory', 'status', 'stockFilter',
            'totalItemsCount', 'availableItemsCount', 'outOfStockCount', 'categoriesCount', 'damagedCount'
        ));
    }

    public function createItem()
    {
        $categories = $this->getItemCategories();

        return view('staff.items.create', compact('categories'));
    }

    public function storeItem(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'laboratory' => 'required|string|in:Alfresco,Kitchen,Food Lab,Hotel',
            'location' => 'required|string|max:255',
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
            $imagePath = \App\Support\Uploads::store($request->file('image'), 'items');
        }

        $item = Item::create([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'laboratory' => $request->laboratory,
            'location' => $request->location,
            'total_stock' => $request->total_stock,
            'available_stock' => $availableStock,
            'image_path' => $imagePath,
        ]);

        // Auto-assign QR code for the item
        $item->update(['qr_code' => InventoryCodes::itemQrCode($item->id)]);

        // Create individual units for tracking
        $this->createUnitsForItem($item);

        return redirect()->route('staff.items.index')
                        ->with('success', 'Item added successfully!');
    }

    public function getItemUnits(Item $item)
    {
        $units = $item->units()->with('currentBorrower:id,name')->get()->map(fn($u) => [
            'id' => $u->id,
            'unit_code' => $u->unit_code,
            'qr_code' => $u->qr_code,
            'status' => $u->status,
            'condition' => $u->condition,
            'current_borrower' => $u->currentBorrower?->name ?? null,
            'notes' => $u->notes,
        ]);

        return response()->json([
            'item_id' => $item->id,
            'units' => $units,
            'item_name' => $item->name,
            'total' => $units->count(),
        ]);
    }

    public function editItem(Item $item)
    {
        $categories = $this->getItemCategories();

        return view('staff.items.edit', compact('item', 'categories'));
    }

    private function getItemCategories()
    {
        return Item::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }

    public function updateItem(Request $request, Item $item)
    {
        $status = $request->status ?? $item->status;
        if ($status === 'retired') {
            $status = 'disposed';
        }

        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'laboratory' => 'required|string|in:Alfresco,Kitchen,Food Lab,Hotel',
            'location' => 'required|string|max:255',
            'total_stock' => 'required|integer|min:0',
            'available_stock' => 'required|integer|min:0',
            'status' => 'nullable|string|in:available,in_use,maintenance,disposed,damaged,lost,retired',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($status !== 'disposed') {
            $rules['total_stock'] = 'required|integer|min:1';
        }

        $request->validate($rules);

        if ($status === 'disposed') {
            if ($item->borrowings()->whereIn('status', ['pending', 'approved', 'issued'])->exists()) {
                return back()->withErrors([
                    'status' => 'Cannot dispose this item while units are still on active borrowings.',
                ])->withInput();
            }
        } elseif ($request->available_stock > $request->total_stock) {
            return back()->withErrors([
                'available_stock' => 'Available stock cannot exceed total stock.',
            ])->withInput();
        }

        $imagePath = $item->image_path;
        if ($request->hasFile('image')) {
            \App\Support\Uploads::delete($imagePath);
            $imagePath = \App\Support\Uploads::store($request->file('image'), 'items');
        }

        $item->update([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'laboratory' => $request->laboratory,
            'location' => $request->location,
            'image_path' => $imagePath,
        ]);

        if ($status === 'disposed') {
            $item->applyDisposedState();
        } else {
            $item->update([
                'status' => $status,
                'total_stock' => $request->total_stock,
                'available_stock' => $request->available_stock,
            ]);

            if ($item->units()->exists()) {
                $item->syncStockFromUnits();
            }
        }

        return redirect()->route('staff.items.index')
                        ->with('success', 'Item updated successfully!');
    }

    public function updateUnitStatus(Request $request, Item $item, \App\Models\ItemUnit $unit)
    {
        if ($unit->item_id !== $item->id) {
            abort(404);
        }

        $request->validate([
            'status' => 'required|string|in:available,borrowed,maintenance,damaged,needs_repair,lost,disposed',
        ]);

        if ($request->status === 'disposed' && $unit->status === 'borrowed') {
            return response()->json([
                'message' => 'Return this unit before marking it as disposed.',
            ], 422);
        }

        $updates = ['status' => $request->status];
        if ($request->status === 'disposed') {
            $updates['current_borrower_id'] = null;
            $updates['borrowing_id'] = null;
        }

        if ($request->status === 'damaged') {
            $unit->markDamaged(['source' => 'manual_status_update']);
        } elseif ($request->status === 'disposed') {
            app(\App\Services\MaintenanceAutoScheduleService::class)
                ->cancelScheduledMaintenanceForUnit($unit, 'Unit disposed — beyond repair.');
            $unit->update($updates);
        } else {
            $unit->update($updates);
        }

        if ($item->units()->where('status', '!=', 'disposed')->count() === 0) {
            $item->applyDisposedState();
        } else {
            if ($item->status === 'disposed') {
                $item->update(['status' => 'available']);
            }
            $item->syncStockFromUnits();
        }

        $item->refresh();

        return response()->json([
            'unit' => $unit->fresh(),
            'item' => [
                'total_stock' => $item->total_stock,
                'available_stock' => $item->available_stock,
                'status' => $item->status,
            ],
        ]);
    }

    public function deleteItem(Item $item)
    {
        // Check if item has active borrowings
        if ($item->borrowings()->whereIn('status', ['pending', 'approved', 'issued'])->exists()) {
            return back()->withErrors(['error' => 'Cannot delete item with active borrowings.']);
        }

        // Soft delete the item (keeps it in database)
        $item->delete();
        
        return redirect()->route('staff.items.index')
                        ->with('success', 'Item moved to trash successfully!');
    }

    /**
     * View trashed (soft deleted) items
     */
    public function trashedItems(Request $request)
    {
        $search = $request->query('search');
        $category = $request->query('category');
        
        $trashedItems = Item::onlyTrashed()
            ->when($search, function($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('asset_code', 'like', "%{$search}%")
                      ->orWhere('qr_code', 'like', "%{$search}%");
                });
            })
            ->when($category, function($query, $category) {
                return $query->where('category', $category);
            })
            ->orderBy('deleted_at', 'desc')
            ->paginate(15);

        $categories = Item::onlyTrashed()->distinct('category')->pluck('category');

        return view('staff.items.trash', compact('trashedItems', 'search', 'category', 'categories'));
    }

    /**
     * Restore a soft deleted item
     */
    public function restoreItem($id)
    {
        $item = Item::withTrashed()->find($id);
        
        if (!$item) {
            return back()->withErrors(['error' => 'Item not found.']);
        }

        $item->restore();
        
        return redirect()->route('staff.items.trash')
                        ->with('success', 'Item restored successfully!');
    }

    /**
     * Permanently delete an item
     */
    public function forceDeleteItem($id)
    {
        $item = Item::withTrashed()->find($id);
        
        if (!$item) {
            return back()->withErrors(['error' => 'Item not found.']);
        }

        // Check if item has any borrowings (even deleted ones)
        if ($item->borrowings()->exists()) {
            return back()->withErrors(['error' => 'Cannot permanently delete item with borrowing records.']);
        }

        // Delete image permanently
        \App\Support\Uploads::delete($item->image_path);

        $item->forceDelete();
        
        return redirect()->route('staff.items.trash')
                        ->with('success', 'Item permanently deleted!');
    }

    /**
     * Search trashed items for live search dropdown
     */
    public function searchTrashedItems(Request $request)
    {
        $search = $request->query('search');
        
        if (!$search || strlen($search) < 1) {
            return response()->json(['items' => []]);
        }

        $items = Item::onlyTrashed()
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('asset_code', 'like', "%{$search}%")
                      ->orWhere('qr_code', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'asset_code', 'qr_code']);

        return response()->json(['items' => $items]);
    }

    /**
     * Bulk import items from Excel/CSV.
     * Required columns: name, category, total_stock, location, laboratory
     */
    public function bulkImportItems(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ]);

        try {
            $import = new \App\Imports\ItemsImport();
            $import->importFromUpload($request->file('import_file'));

            return $this->bulkImportFlashResponse(
                $import->getImportedCount(),
                $import->getErrors()
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import file: ' . $e->getMessage());
        }
    }

    private function bulkImportFlashResponse(int $importedCount, array $errors)
    {
        $errorSummary = '';
        if (count($errors) > 0) {
            $errorSummary = ' ' . implode(' | ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $errorSummary .= ' (and ' . (count($errors) - 5) . ' more)';
            }
        }

        if ($importedCount === 0) {
            $message = 'No items were imported.';
            if ($errorSummary !== '') {
                $message .= $errorSummary;
            }

            return back()->with('error', $message);
        }

        $message = "Successfully imported {$importedCount} item(s).";
        if ($errorSummary !== '') {
            $message .= ' Some rows were skipped —' . $errorSummary;
        }

        return back()->with('success', $message);
    }

    /**
     * Direct Borrowing for Staff/Admin
     */
    public function borrowForm(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');

        // Build query with optional search & category filters
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

        $availableItems = $query->orderBy('name')->paginate(24)->withQueryString();

        // Get all categories for filter
        $categories = Item::borrowable()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        // Pre-selected item (if coming from dashboard)
        $selectedItemId = $request->get('item_id');
        $selectedItem = $selectedItemId ? Item::find($selectedItemId) : null;

        return view('staff.borrowings.borrow', compact(
            'availableItems',
            'categories',
            'selectedItem',
            'search',
            'category'
        ));
    }

    /**
     * API endpoint for item management search (all items, any stock level)
     */
    public function searchAllItems(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 1) {
            return response()->json(['items' => []]);
        }

        $term = '%' . $q . '%';
        $startsWith = $q . '%';

        $items = Item::where(function ($query) use ($term) {
                $query->where('name', 'like', $term)
                      ->orWhere('asset_code', 'like', $term);
            })
            ->orderByRaw('CASE WHEN name LIKE ? THEN 0 ELSE 1 END', [$startsWith])
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'category', 'available_stock']);

        return response()->json(['items' => $items]);
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

        return response()->json($items->map(fn (Item $item) => [
            'id' => $item->id,
            'name' => $item->name,
            'category' => $item->category,
            'laboratory' => $item->laboratory,
            'location' => $item->location,
            'available_stock' => $item->available_stock,
            'asset_code' => $item->asset_code,
            'image_url' => $item->image_url,
        ]));
    }

    public function borrowItem(\App\Http\Requests\DirectBorrowRequest $request)
    {
        try {
            $borrowings = $this->borrowingService->createMultipleDirectBorrows(
                $request->validated('items'),
                (int) $request->validated('return_hours'),
                $request->input('notes')
            );

            $count = count($borrowings);
            $message = $count === 1
                ? 'Item borrowed successfully!'
                : "{$count} items borrowed successfully!";

            return redirect()
                ->route('staff.borrowings.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
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

        $borrowings = Borrowing::with(['user', 'item', 'itemUnit', 'approver', 'issuer', 'rejector', 'returnedToUser'])
            ->when($status, function($query, $status) {
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

        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:3', 'max:500'],
        ]);

        try {
            // Use transaction to ensure consistency
            \DB::transaction(function () use ($request, $borrowing) {
                // Return stock to available (stock was decremented when request was created)
                $borrowing->item->increment('available_stock', $borrowing->quantity);

                $borrowing->update([
                    'status' => 'rejected',
                    'rejection_reason' => $request->rejection_reason,
                    'rejected_by' => auth()->id(),
                    'rejected_date' => now(),
                ]);

                // Send DB notification to the student
                Notification::create([
                    'user_id' => $borrowing->user_id,
                    'type' => 'danger',
                    'title' => 'Borrow Request Rejected',
                    'message' => 'Your request to borrow "' . $borrowing->item->name . '" has been rejected. Reason: ' . $request->rejection_reason,
                    'action_url' => route('student.borrowings.index'),
                    'priority' => 'medium',
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

                // Assign an available unit to this borrowing
                $unit = ItemUnit::where('item_id', $borrowing->item_id)
                    ->where('status', 'available')
                    ->lockForUpdate()
                    ->first();

                $updateData = [
                    'status' => 'issued',
                    'issued_date' => now(),
                    'issued_by' => auth()->id(),
                ];

                if ($unit) {
                    $unit->markBorrowed($borrowing->user_id, $borrowing->id);
                    $updateData['item_unit_id'] = $unit->id;
                }

                $borrowing->update($updateData);
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
            'return_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $returnImagePath = null;
        if ($request->hasFile('return_image')) {
            $returnImagePath = \App\Support\Uploads::store($request->file('return_image'), 'return-images');
        }

        try {
            $isOverdue = $borrowing->expected_return_date && $borrowing->expected_return_date < now();
            $overdueDays = $isOverdue ? (int) now()->diffInDays($borrowing->expected_return_date) : 0;

            \DB::transaction(function () use ($borrowing, $request, $isOverdue, $overdueDays, $returnImagePath) {
                // Lock the item for update to prevent race conditions
                $item = Item::lockForUpdate()->findOrFail($borrowing->item_id);

                // Return stock to available
                $item->increment('available_stock', $borrowing->quantity);

                // Update item wear level based on return condition
                if (in_array($request->return_condition, ['needs_repair', 'damaged'])) {
                    $unitCount = max(1, $item->total_stock ?: 1);
                    $wearIncrease = $request->return_condition === 'damaged'
                        ? (int) max(5, round(30 / $unitCount))
                        : (int) max(3, round(15 / $unitCount));
                    $item->wear_level = min(100, $item->wear_level + $wearIncrease);
                    
                    // Damaged/needs_repair units are not available, so decrement available stock
                    $item->decrement('available_stock', $borrowing->quantity);
                    $item->save();
                } elseif ($request->return_condition === 'fair') {
                    $item->wear_level = min(100, $item->wear_level + 5);
                    $item->save();
                }

                $borrowing->update([
                    'status' => 'returned',
                    'returned_date' => now(),
                    'returned_to' => auth()->id(),
                    'return_condition' => $request->return_condition,
                    'return_notes' => $request->return_notes,
                    'return_image_path' => $returnImagePath,
                ]);

                // Release or mark the assigned unit based on condition
                if ($borrowing->item_unit_id) {
                    $unit = ItemUnit::find($borrowing->item_unit_id);
                    if ($unit) {
                        if (in_array($request->return_condition, ['damaged', 'needs_repair'], true)) {
                            $returnNotes = $request->return_notes ? ': ' . $request->return_notes : '.';
                            $issuesFound = $request->return_condition === 'damaged'
                                ? 'Returned damaged' . $returnNotes
                                : 'Returned — needs repair' . $returnNotes;

                            $unit->markDamaged([
                                'return_condition' => $request->return_condition,
                                'notes' => $request->return_notes,
                                'issues_found' => $issuesFound,
                            ]);
                        } else {
                            $unit->markReturned();
                        }
                    }
                }

                // Notify the student about the return
                $conditionLabel = ucfirst(str_replace('_', ' ', $request->return_condition));
                $overdueNote = $isOverdue ? " (returned {$overdueDays} day(s) late)" : '';
                
                Notification::create([
                    'user_id' => $borrowing->user_id,
                    'type' => in_array($request->return_condition, ['good', 'fair']) ? 'success' : 'warning',
                    'title' => 'Item Returned',
                    'message' => 'Your borrowed item "' . $item->name . '" has been marked as returned. Condition: ' . $conditionLabel . $overdueNote,
                    'action_url' => route('student.borrowings.index'),
                    'priority' => $isOverdue || in_array($request->return_condition, ['needs_repair', 'damaged']) ? 'high' : 'low',
                ]);

            });

            $successMsg = 'Item returned successfully! Condition: ' . ucfirst(str_replace('_', ' ', $request->return_condition));
            if ($isOverdue) {
                $successMsg .= " (was {$overdueDays} day(s) overdue)";
            }

            return back()->with('success', $successMsg);
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
            'priority' => 'medium',
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
            'priority' => 'medium',
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

    /**
     * Create individual trackable units for an item.
     * Each unit gets a unique code and QR code.
     */
    private function createUnitsForItem(Item $item): void
    {
        $itemPad = str_pad($item->id, 6, '0', STR_PAD_LEFT);

        for ($i = 1; $i <= $item->total_stock; $i++) {
            $unitCode = InventoryCodes::unitCode($item->id, $i);
            $qrCode = $unitCode . '-' . strtoupper(\Illuminate\Support\Str::random(6));

            \App\Models\ItemUnit::create([
                'item_id' => $item->id,
                'unit_code' => $unitCode,
                'qr_code' => $qrCode,
                'status' => 'available',
                'condition' => 'good',
            ]);
        }
    }
}