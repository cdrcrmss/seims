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
}
