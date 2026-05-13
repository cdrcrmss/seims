<?php

namespace App\Services;

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\Notification;
use App\Models\User;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BorrowingService
{
    /**
     * Create a new borrowing request.
     *
     * @param  array  $data  Validated request data (item_id, quantity, expected_return_date, notes)
     * @param  int|null  $userId  Override user ID (defaults to Auth::id())
     * @return Borrowing
     *
     * @throws \Exception
     */
    public function createBorrowRequest(array $data, ?int $userId = null): Borrowing
    {
        $userId = $userId ?? Auth::id();
        $borrowing = null;
        $settings = AdminController::loadSettings();

        // Check max borrow days
        $maxDays = $settings['max_borrow_days'] ?? 7;
        $expectedReturn = \Carbon\Carbon::parse($data['expected_return_date']);
        if ($expectedReturn->diffInDays(now()) > $maxDays) {
            throw new \Exception("Maximum borrowing period is {$maxDays} days.");
        }

        DB::transaction(function () use ($data, $userId, &$borrowing) {
            // Lock the item row to prevent race conditions
            $item = Item::lockForUpdate()->findOrFail($data['item_id']);

            // Check stock availability
            if ($item->available_stock < $data['quantity']) {
                throw new \Exception('Not enough stock available for this item.');
            }

            // Check for duplicate pending request
            $existingRequest = Borrowing::where('user_id', $userId)
                ->where('item_id', $item->id)
                ->where('status', 'pending')
                ->exists();

            if ($existingRequest) {
                throw new \Exception('You already have a pending request for this item.');
            }

            // Decrement available stock atomically
            $item->decrement('available_stock', $data['quantity']);

            // Create borrowing request
            $borrowing = Borrowing::create([
                'user_id' => $userId,
                'item_id' => $item->id,
                'quantity' => $data['quantity'],
                'status' => 'pending',
                'requested_date' => now(),
                'expected_return_date' => $data['expected_return_date'],
                'notes' => $data['notes'] ?? null,
            ]);


            // Notify staff about new borrow request
            $staffUsers = User::whereIn('role', ['staff', 'admin'])->get();
            $student = User::find($userId);
            foreach ($staffUsers as $staff) {
                Notification::create([
                    'user_id' => $staff->id,
                    'type' => 'info',
                    'title' => 'New Borrow Request',
                    'message' => ($student->name ?? 'A student') . ' requested to borrow ' . $data['quantity'] . 'x ' . $item->name,
                    'action_url' => route('staff.borrowings.index', ['status' => 'pending']),
                    'priority' => 'medium',
                ]);
            }
        });

        return $borrowing;
    }

    /**
     * Cancel a borrowing request.
     *
     * @param  Borrowing  $borrowing
     * @param  string|null  $reason
     * @return void
     *
     * @throws \Exception
     */
    public function cancelBorrowRequest(Borrowing $borrowing, ?string $reason = null): void
    {
        if ($borrowing->status !== 'pending') {
            throw new \Exception('Only pending requests can be cancelled.');
        }

        DB::transaction(function () use ($borrowing, $reason) {
            $item = Item::lockForUpdate()->findOrFail($borrowing->item_id);

            // Restore the reserved stock
            $item->increment('available_stock', $borrowing->quantity);

            $borrowing->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason ?? 'Cancelled by student',
                'rejected_date' => now(),
            ]);
        });
    }

    /**
     * Request a borrowing extension.
     *
     * @param  Borrowing  $borrowing
     * @param  string  $newReturnDate
     * @param  string|null  $reason
     * @return void
     *
     * @throws \Exception
     */
    public function requestExtension(Borrowing $borrowing, string $newReturnDate, ?string $reason = null): void
    {
        if (!in_array($borrowing->status, ['approved', 'issued'])) {
            throw new \Exception('Only approved or issued borrowings can be extended.');
        }

        if (\Carbon\Carbon::parse($newReturnDate)->lte($borrowing->expected_return_date)) {
            throw new \Exception('New return date must be after the current return date.');
        }

        DB::transaction(function () use ($borrowing, $newReturnDate, $reason) {
            $borrowing->update([
                'extension_requested' => true,
                'extension_date' => $newReturnDate,
                'extension_reason' => $reason,
                'extension_status' => 'pending',
            ]);

            // Notify staff
            $staffUsers = User::whereIn('role', ['staff', 'admin'])->get();
            $student = $borrowing->user;
            foreach ($staffUsers as $staff) {
                Notification::create([
                    'user_id' => $staff->id,
                    'type' => 'warning',
                    'title' => 'Extension Request',
                    'message' => ($student->name ?? 'A student') . ' requested to extend borrowing of ' . $borrowing->item->name . ' until ' . $newReturnDate,
                    'action_url' => route('staff.borrowings.index'),
                    'priority' => 'medium',
                ]);
            }
        });
    }

    /**
     * Create a direct borrowing for Staff/Admin (skips approval, goes directly to issued).
     *
     * @param  array  $data  Validated request data (item_id, quantity, expected_return_date, notes)
     * @param  int|null  $userId  Override user ID (defaults to Auth::id())
     * @return Borrowing
     *
     * @throws \Exception
     */
    public function createDirectBorrow(array $data, ?int $userId = null): Borrowing
    {
        $userId = $userId ?? Auth::id();
        $borrowing = null;
        $settings = AdminController::loadSettings();

        // Check max borrow days
        $maxDays = $settings['max_borrow_days'] ?? 7;
        $expectedReturn = \Carbon\Carbon::parse($data['expected_return_date']);
        if ($expectedReturn->diffInDays(now()) > $maxDays) {
            throw new \Exception("Maximum borrowing period is {$maxDays} days.");
        }

        DB::transaction(function () use ($data, $userId, &$borrowing) {
            // Lock the item row to prevent race conditions
            $item = Item::lockForUpdate()->findOrFail($data['item_id']);

            // Check stock availability
            if ($item->available_stock < $data['quantity']) {
                throw new \Exception('Not enough stock available for this item.');
            }

            // Decrement available stock atomically
            $item->decrement('available_stock', $data['quantity']);

            // Assign an available unit to this borrowing
            $unit = \App\Models\ItemUnit::where('item_id', $item->id)
                ->where('status', 'available')
                ->lockForUpdate()
                ->first();

            // Create borrowing record directly as "issued" (skip pending/approved)
            $borrowing = Borrowing::create([
                'user_id' => $userId,
                'item_id' => $item->id,
                'item_unit_id' => $unit ? $unit->id : null,
                'quantity' => $data['quantity'],
                'status' => 'issued',
                'requested_date' => now(),
                'approved_date' => now(),
                'approved_by' => $userId,
                'issued_date' => now(),
                'issued_by' => $userId,
                'expected_return_date' => $data['expected_return_date'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Mark the unit as borrowed
            if ($unit) {
                $unit->markBorrowed($userId, $borrowing->id);
            }
        });

        return $borrowing;
    }
}
