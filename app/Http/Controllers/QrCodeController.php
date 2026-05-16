<?php

namespace App\Http\Controllers;

use App\Models\Item;
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

        // Assign QR code value to item if not set (use unique ID with random component)
        if (!$item->qr_code) {
            $item->update(['qr_code' => 'SEIMS-' . str_pad($item->id, 6, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(8))]);
            $item->refresh();
        }

        // QR encodes the unique code string so scanners can look it up
        $qrData = $item->qr_code;

        return view('qr.display', compact('item', 'qrData'));
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

        $isStaffOrAdmin = $user && in_array($user->role, ['staff', 'admin'], true);

        // Holder details only for staff/admin
        if ($isStaffOrAdmin) {
            $payload['current_holders'] = $item->borrowings()
                ->where('status', 'issued')
                ->with('user:id,name')
                ->get()
                ->map(fn($b) => [
                    'user_name' => $b->user->name,
                    'quantity' => $b->quantity,
                    'expected_return_date' => $b->expected_return_date?->format('M d, Y') ?? 'N/A',
                ]);
        } else {
            $payload['current_holders'] = [];
        }

        // Extended fields only for staff/admin
        if ($isStaffOrAdmin) {
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
            
            // Include issued borrowings (ready for return) — multiple rows are normal when
            // the same catalog item has several units out on loan (each unit has its own QR).
            $payload['issued_borrowings'] = $item->borrowings()
                ->where('status', 'issued')
                ->with(['user:id,name', 'itemUnit:id,unit_code'])
                ->get()
                ->map(fn ($b) => [
                    'id' => $b->id,
                    'user_name' => $b->user->name,
                    'quantity' => $b->quantity,
                    'expected_return_date' => $b->expected_return_date?->format('M d, Y') ?? 'N/A',
                    'unit_code' => $b->itemUnit?->unit_code,
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

        // First try item-level QR code
        $foundItem = Item::where('qr_code', $code)->first();

        if ($foundItem) {
            return response()->json([
                'success' => true,
                'item' => $this->buildItemPayload($foundItem),
            ]);
        }

        // Then try unit-level QR code
        $unit = \App\Models\ItemUnit::where('qr_code', $code)
            ->orWhere('unit_code', $code)
            ->with(['item', 'currentBorrower:id,name'])
            ->first();

        if ($unit) {
            $user = Auth::user();
            $isStaffOrAdmin = $user && in_array($user->role, ['staff', 'admin'], true);

            if (!$isStaffOrAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unit-level QR codes can only be scanned by staff.',
                ], 403);
            }

            $payload = $this->buildItemPayload($unit->item);
            $payload['unit'] = [
                'unit_code' => $unit->unit_code,
                'qr_code' => $unit->qr_code,
                'status' => $unit->status,
                'condition' => $unit->condition,
                'current_borrower' => $unit->currentBorrower?->name ?? null,
                'notes' => $unit->notes,
            ];

            return response()->json([
                'success' => true,
                'item' => $payload,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Item not found.'], 404);
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
                'qr_code' => 'SEIMS-' . str_pad($item->id, 6, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(8))
            ]);
            $count++;
        }

        return back()->with('success', "Generated QR codes for {$count} items.");
    }
}
