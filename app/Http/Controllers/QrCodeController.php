<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Borrowing;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\ScanEvent;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QrCodeController extends Controller
{
    /**
     * Show QR scanner page
     */
    public function scanner()
    {
        return view('qr.scanner');
    }

    /**
     * Generate QR code for an item (returns SVG)
     * Restricted to staff/admin via route middleware.
     */
    public function generate(Item $item)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['staff', 'admin'])) {
            abort(403, 'Unauthorized: only staff or admin can generate QR codes.');
        }

        // Generate QR code data string
        $qrData = route('qr.lookup', $item->id);

        // Assign QR code value to item if not set (use deterministic code based on item ID)
        if (!$item->qr_code) {
            $item->update(['qr_code' => 'SEIMS-' . str_pad($item->id, 6, '0', STR_PAD_LEFT) . '-' . strtoupper(substr(md5($item->name . $item->id), 0, 6))]);
        }

        // Generate QR code URL using a reliable fallback approach
        // Primary: Google Charts API (no data leakage - only contains our public URL)
        $qrImageUrl = 'https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=' . urlencode($qrData)
            . '&choe=UTF-8';

        return view('qr.display', compact('item', 'qrImageUrl', 'qrData'));
    }

    /**
     * Build the item JSON payload.
     * Students get a limited set of non-sensitive fields;
     * staff/admin get the full details.
     */
    private function buildItemPayload(Item $item): array
    {
        $user = Auth::user();

        // Base fields visible to all authenticated users (including students)
        $payload = [
            'id' => $item->id,
            'name' => $item->name,
            'category' => $item->category,
            'available_stock' => $item->available_stock,
            'status' => $item->status ?? 'available',
            'location' => $item->location ?? 'N/A',
        ];

        // Extended fields only for staff/admin
        if ($user && in_array($user->role, ['staff', 'admin'])) {
            $payload['total_stock'] = $item->total_stock;
            $payload['wear_level'] = $item->wear_level;
            $payload['qr_code'] = $item->qr_code;
            $payload['description'] = $item->description;
            
            // Include pending borrowings (approved, ready for issuance)
            $payload['pending_borrowings'] = $item->borrowings()
                ->where('status', 'approved')
                ->with('user:id,name')
                ->get()
                ->map(fn($b) => [
                    'id' => $b->id,
                    'user_name' => $b->user->name,
                    'quantity' => $b->quantity,
                    'status' => $b->status,
                ]);
            
            // Include issued borrowings (ready for return)
            $payload['issued_borrowings'] = $item->borrowings()
                ->where('status', 'issued')
                ->with('user:id,name')
                ->get()
                ->map(fn($b) => [
                    'id' => $b->id,
                    'user_name' => $b->user->name,
                    'quantity' => $b->quantity,
                    'expected_return_date' => $b->expected_return_date?->format('M d, Y') ?? 'N/A',
                ]);
        }

        return $payload;
    }

    /**
     * Lookup item by QR code value or ID
     */
    public function lookup(Request $request, ?Item $item = null)
    {
        if ($item) {
            return response()->json([
                'success' => true,
                'item' => $this->buildItemPayload($item),
            ]);
        }

        // Lookup by QR code string
        $code = $request->input('code');
        $foundItem = Item::where('qr_code', $code)->first();

        if (!$foundItem) {
            return response()->json(['success' => false, 'message' => 'Item not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'item' => $this->buildItemPayload($foundItem),
        ]);
    }

    /**
     * Batch generate QR codes for all items
     * Restricted to staff/admin via route middleware.
     */
    public function batchGenerate()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['staff', 'admin'])) {
            abort(403, 'Unauthorized: only staff or admin can batch-generate QR codes.');
        }

        $items = Item::whereNull('qr_code')->orWhere('qr_code', '')->get();
        $count = 0;

        foreach ($items as $item) {
            $item->update([
                'qr_code' => 'SEIMS-' . str_pad($item->id, 6, '0', STR_PAD_LEFT) . '-' . strtoupper(substr(md5($item->name . $item->id), 0, 6))
            ]);
            $count++;
        }

        return back()->with('success', "Generated QR codes for {$count} items.");
    }

    /**
     * Unified scan action endpoint.
     * Accepts a QR code and an intended action, validates context, executes action.
     * Returns JSON with outcome (success/warning/blocked) for UX feedback.
     */
    public function scanAction(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
            'action' => 'required|in:issue,return,room_check_in,lookup',
        ]);

        $user = Auth::user();
        $qrCode = $request->input('qr_code');
        $action = $request->input('action');

        // Parse the QR code — could be a URL or a raw code
        $item = $this->resolveItemFromQr($qrCode);
        $room = $this->resolveRoomFromQr($qrCode);

        switch ($action) {
            case 'issue':
                return $this->handleIssueScan($user, $item, $qrCode);
            case 'return':
                return $this->handleReturnScan($user, $item, $qrCode);
            case 'room_check_in':
                return $this->handleRoomCheckIn($user, $room, $qrCode);
            case 'lookup':
                return $this->handleLookupScan($user, $item, $room, $qrCode);
            default:
                return response()->json(['outcome' => 'blocked', 'message' => 'Unknown action.'], 400);
        }
    }

    /**
     * Issue a borrowing by scanning the item QR code.
     */
    private function handleIssueScan($user, ?Item $item, string $qrCode)
    {
        if (!in_array($user->role, ['staff', 'admin'])) {
            ScanEvent::log(ScanEvent::ACTION_BORROW_ISSUE, 'item', $item?->id ?? 0, ScanEvent::OUTCOME_BLOCKED, 'Unauthorized role', null, $qrCode);
            return response()->json(['outcome' => 'blocked', 'message' => 'Only staff/admin can issue items.']);
        }

        if (!$item) {
            ScanEvent::log(ScanEvent::ACTION_BORROW_ISSUE, 'item', 0, ScanEvent::OUTCOME_BLOCKED, 'Item not found', null, $qrCode);
            return response()->json(['outcome' => 'blocked', 'message' => 'Item not found for this QR code.']);
        }

        // Find the next approved borrowing for this item
        $borrowing = Borrowing::where('item_id', $item->id)
            ->where('status', 'approved')
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$borrowing) {
            ScanEvent::log(ScanEvent::ACTION_BORROW_ISSUE, 'item', $item->id, ScanEvent::OUTCOME_WARNING, 'No approved borrowing found', null, $qrCode);
            return response()->json([
                'outcome' => 'warning',
                'message' => 'No approved borrowing awaiting issuance for this item.',
                'item' => ['id' => $item->id, 'name' => $item->name],
            ]);
        }

        // Execute the issue
        try {
            \DB::transaction(function () use ($borrowing, $user) {
                $lockItem = Item::lockForUpdate()->findOrFail($borrowing->item_id);
                if ($lockItem->available_stock < 0) {
                    throw new \Exception('Insufficient stock.');
                }
                $borrowing->update([
                    'status' => 'issued',
                    'issued_date' => now(),
                    'issued_by' => $user->id,
                ]);
            });
        } catch (\Exception $e) {
            ScanEvent::log(ScanEvent::ACTION_BORROW_ISSUE, 'item', $item->id, ScanEvent::OUTCOME_BLOCKED, $e->getMessage(), ['borrowing_id' => $borrowing->id], $qrCode);
            return response()->json(['outcome' => 'blocked', 'message' => 'Issue failed: ' . $e->getMessage()]);
        }

        ScanEvent::log(ScanEvent::ACTION_BORROW_ISSUE, 'item', $item->id, ScanEvent::OUTCOME_SUCCESS, 'Issued to ' . $borrowing->user?->name, [
            'borrowing_id' => $borrowing->id,
            'student_name' => $borrowing->user?->name,
            'quantity' => $borrowing->quantity,
        ], $qrCode);

        return response()->json([
            'outcome' => 'success',
            'message' => 'Item issued to ' . ($borrowing->user?->name ?? 'student') . ' (Qty: ' . $borrowing->quantity . ')',
            'item' => ['id' => $item->id, 'name' => $item->name],
            'borrowing' => ['id' => $borrowing->id, 'user' => $borrowing->user?->name, 'quantity' => $borrowing->quantity],
        ]);
    }

    /**
     * Return a borrowing by scanning the item QR code.
     */
    private function handleReturnScan($user, ?Item $item, string $qrCode)
    {
        if (!in_array($user->role, ['staff', 'admin'])) {
            ScanEvent::log(ScanEvent::ACTION_BORROW_RETURN, 'item', $item?->id ?? 0, ScanEvent::OUTCOME_BLOCKED, 'Unauthorized role', null, $qrCode);
            return response()->json(['outcome' => 'blocked', 'message' => 'Only staff/admin can process returns.']);
        }

        if (!$item) {
            ScanEvent::log(ScanEvent::ACTION_BORROW_RETURN, 'item', 0, ScanEvent::OUTCOME_BLOCKED, 'Item not found', null, $qrCode);
            return response()->json(['outcome' => 'blocked', 'message' => 'Item not found for this QR code.']);
        }

        // Find issued borrowings for this item
        $issuedBorrowings = Borrowing::where('item_id', $item->id)
            ->where('status', 'issued')
            ->with('user:id,name')
            ->orderBy('issued_date', 'asc')
            ->get();

        if ($issuedBorrowings->isEmpty()) {
            ScanEvent::log(ScanEvent::ACTION_BORROW_RETURN, 'item', $item->id, ScanEvent::OUTCOME_WARNING, 'No issued borrowings', null, $qrCode);
            return response()->json([
                'outcome' => 'warning',
                'message' => 'No active borrowings to return for this item.',
                'item' => ['id' => $item->id, 'name' => $item->name],
            ]);
        }

        // Check for overdue
        $hasOverdue = $issuedBorrowings->filter(fn($b) => $b->expected_return_date && $b->expected_return_date < now())->isNotEmpty();

        ScanEvent::log(ScanEvent::ACTION_BORROW_RETURN, 'item', $item->id, $hasOverdue ? ScanEvent::OUTCOME_WARNING : ScanEvent::OUTCOME_SUCCESS, 'Found ' . $issuedBorrowings->count() . ' active borrowing(s)', [
            'borrowing_ids' => $issuedBorrowings->pluck('id'),
            'has_overdue' => $hasOverdue,
        ], $qrCode);

        return response()->json([
            'outcome' => $hasOverdue ? 'warning' : 'success',
            'message' => $hasOverdue
                ? 'This item has overdue borrowings! Select which to return.'
                : $issuedBorrowings->count() . ' active borrowing(s) found. Select which to return.',
            'item' => ['id' => $item->id, 'name' => $item->name],
            'borrowings' => $issuedBorrowings->map(fn($b) => [
                'id' => $b->id,
                'user_name' => $b->user?->name ?? 'Unknown',
                'quantity' => $b->quantity,
                'issued_date' => $b->issued_date?->format('M d, Y'),
                'expected_return_date' => $b->expected_return_date?->format('M d, Y'),
                'is_overdue' => $b->expected_return_date && $b->expected_return_date < now(),
            ]),
            'requires_condition' => true,
        ]);
    }

    /**
     * Room check-in via QR scan.
     */
    private function handleRoomCheckIn($user, ?Room $room, string $qrCode)
    {
        if (!$room) {
            ScanEvent::log(ScanEvent::ACTION_ROOM_CHECK_IN, 'room', 0, ScanEvent::OUTCOME_BLOCKED, 'Room not found', null, $qrCode);
            return response()->json(['outcome' => 'blocked', 'message' => 'Room not found for this QR code.']);
        }

        // Find an approved reservation for this room within the check-in window (15 min before to 15 min after start)
        $now = now();
        $reservation = Reservation::where('room_id', $room->id)
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('start_datetime', '<=', $now->copy()->addMinutes(15))
            ->where('start_datetime', '>=', $now->copy()->subMinutes(15))
            ->first();

        if (!$reservation) {
            // Check if there's a future reservation today
            $futureReservation = Reservation::where('room_id', $room->id)
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereDate('start_datetime', today())
                ->where('start_datetime', '>', $now)
                ->first();

            if ($futureReservation) {
                $startsIn = $now->diffInMinutes($futureReservation->start_datetime);
                ScanEvent::log(ScanEvent::ACTION_ROOM_CHECK_IN, 'room', $room->id, ScanEvent::OUTCOME_WARNING, 'Too early for check-in', [
                    'reservation_id' => $futureReservation->id,
                    'starts_in_minutes' => $startsIn,
                ], $qrCode);
                return response()->json([
                    'outcome' => 'warning',
                    'message' => "Too early! Your reservation starts in {$startsIn} minutes. Check-in opens 15 minutes before start.",
                    'room' => ['id' => $room->id, 'name' => $room->name],
                ]);
            }

            ScanEvent::log(ScanEvent::ACTION_ROOM_CHECK_IN, 'room', $room->id, ScanEvent::OUTCOME_BLOCKED, 'No matching reservation', null, $qrCode);
            return response()->json([
                'outcome' => 'blocked',
                'message' => 'No approved reservation found for you in this room at this time.',
                'room' => ['id' => $room->id, 'name' => $room->name],
            ]);
        }

        // Perform check-in
        $reservation->update([
            'status' => 'checked_in',
            'checked_in_at' => $now,
        ]);

        ScanEvent::log(ScanEvent::ACTION_ROOM_CHECK_IN, 'room', $room->id, ScanEvent::OUTCOME_SUCCESS, 'Checked in successfully', [
            'reservation_id' => $reservation->id,
            'room_name' => $room->name,
        ], $qrCode);

        return response()->json([
            'outcome' => 'success',
            'message' => 'Checked in to ' . $room->name . '! Your reservation is now active.',
            'room' => ['id' => $room->id, 'name' => $room->name],
            'reservation' => [
                'id' => $reservation->id,
                'start' => $reservation->start_datetime->format('g:ia'),
                'end' => $reservation->end_datetime->format('g:ia'),
                'purpose' => $reservation->purpose,
            ],
        ]);
    }

    /**
     * Simple lookup scan — log the event and return item/room data.
     */
    private function handleLookupScan($user, ?Item $item, ?Room $room, string $qrCode)
    {
        if ($item) {
            ScanEvent::log(ScanEvent::ACTION_ITEM_LOOKUP, 'item', $item->id, ScanEvent::OUTCOME_SUCCESS, 'Item looked up', null, $qrCode);
            return response()->json([
                'outcome' => 'success',
                'message' => 'Item found: ' . $item->name,
                'type' => 'item',
                'item' => $this->buildItemPayload($item),
            ]);
        }

        if ($room) {
            ScanEvent::log(ScanEvent::ACTION_ITEM_LOOKUP, 'room', $room->id, ScanEvent::OUTCOME_SUCCESS, 'Room looked up', null, $qrCode);
            return response()->json([
                'outcome' => 'success',
                'message' => 'Room found: ' . $room->name,
                'type' => 'room',
                'room' => [
                    'id' => $room->id,
                    'name' => $room->name,
                    'building' => $room->building ?? null,
                    'capacity' => $room->capacity,
                    'status' => $room->status,
                ],
            ]);
        }

        ScanEvent::log(ScanEvent::ACTION_ITEM_LOOKUP, 'item', 0, ScanEvent::OUTCOME_BLOCKED, 'Nothing found', null, $qrCode);
        return response()->json(['outcome' => 'blocked', 'message' => 'No item or room found for this QR code.']);
    }

    /**
     * Resolve an Item from a QR code value (URL or raw code).
     */
    private function resolveItemFromQr(string $qrCode): ?Item
    {
        // Try by qr_code field
        $item = Item::where('qr_code', $qrCode)->first();
        if ($item) return $item;

        // Try extracting item ID from URL (e.g., /qr/lookup/5)
        if (preg_match('/\/qr\/lookup\/(\d+)/', $qrCode, $matches)) {
            return Item::find($matches[1]);
        }

        // Try by ID directly if numeric
        if (is_numeric($qrCode)) {
            return Item::find($qrCode);
        }

        return null;
    }

    /**
     * Resolve a Room from a QR code value.
     */
    private function resolveRoomFromQr(string $qrCode): ?Room
    {
        // Room QR codes use format: SEIMS-ROOM-{id}-{hash}
        if (preg_match('/SEIMS-ROOM-(\d+)/', $qrCode, $matches)) {
            return Room::find($matches[1]);
        }

        // Try by room_code field if it exists
        return Room::where('room_code', $qrCode)->first();
    }

    /**
     * Generate QR code for a room.
     */
    public function generateForRoom(Room $room)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['staff', 'admin'])) {
            abort(403);
        }

        $roomCode = 'SEIMS-ROOM-' . str_pad($room->id, 4, '0', STR_PAD_LEFT) . '-' . strtoupper(substr(md5($room->name . $room->id), 0, 6));

        // Store room code if not set
        if (!$room->room_code) {
            $room->update(['room_code' => $roomCode]);
        } else {
            $roomCode = $room->room_code;
        }

        $qrImageUrl = 'https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=' . urlencode($roomCode) . '&choe=UTF-8';

        return view('qr.display-room', compact('room', 'qrImageUrl', 'roomCode'));
    }

    /**
     * Get scan event history (staff/admin only).
     */
    public function scanHistory(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['staff', 'admin'])) {
            abort(403);
        }

        $events = ScanEvent::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Stats from full DB, not just current page
        $scanStats = [
            'today' => ScanEvent::whereDate('created_at', today())->count(),
            'success' => ScanEvent::where('outcome', 'success')->count(),
            'warning' => ScanEvent::where('outcome', 'warning')->count(),
            'blocked' => ScanEvent::where('outcome', 'blocked')->count(),
        ];

        return view('qr.scan-history', compact('events', 'scanStats'));
    }
}
