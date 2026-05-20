<?php

namespace App\Http\Requests;

use App\Models\Borrowing;
use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Validator;
use Illuminate\Validation\ValidationException;

class StudentBorrowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'student';
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1|max:20',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'return_hours' => 'required|integer|in:3,4,5,6,7,8',
            'purpose' => 'required|string|min:10|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Add at least one item to your request.',
            'items.max' => 'You can request up to 20 different items at once.',
            'return_hours.in' => 'Return time must be between 3 and 8 hours.',
            'purpose.required' => 'Please describe why you need these items.',
            'purpose.min' => 'Purpose must be at least 10 characters.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $userId = Auth::id();

            $hasOverdue = Borrowing::where('user_id', $userId)
                ->where('status', 'issued')
                ->where('expected_return_date', '<', now())
                ->exists();

            if ($hasOverdue) {
                $validator->errors()->add('items', 'Return your overdue items before submitting new requests.');

                return;
            }

            $quantitiesByItem = [];
            foreach ($this->input('items', []) as $line) {
                $itemId = (int) ($line['item_id'] ?? 0);
                $qty = (int) ($line['quantity'] ?? 0);
                $quantitiesByItem[$itemId] = ($quantitiesByItem[$itemId] ?? 0) + $qty;
            }

            foreach ($quantitiesByItem as $itemId => $totalQty) {
                $item = Item::find($itemId);
                if (! $item) {
                    continue;
                }

                if ($totalQty > $item->available_stock) {
                    $validator->errors()->add(
                        'items',
                        "Not enough stock for {$item->name}. Available: {$item->available_stock}."
                    );

                    return;
                }

                $existingPending = Borrowing::where('user_id', $userId)
                    ->where('item_id', $itemId)
                    ->where('status', 'pending')
                    ->exists();

                if ($existingPending) {
                    $validator->errors()->add(
                        'items',
                        "You already have a pending request for {$item->name}."
                    );

                    return;
                }
            }
        });
    }

    protected function passedValidation(): void
    {
        $key = 'borrow-request:' . Auth::id();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'rate_limit' => "You are submitting requests too quickly. Please wait {$seconds} seconds before trying again.",
            ]);
        }

        RateLimiter::hit($key, 300);
    }
}
