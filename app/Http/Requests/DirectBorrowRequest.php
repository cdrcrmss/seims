<?php

namespace App\Http\Requests;

use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class DirectBorrowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $rules = [
            'items' => 'required|array|min:1|max:10',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1|max:10',
            'return_hours' => 'required|integer|in:3,4,5,6,7,8',
        ];

        if (Auth::user()?->role === 'student') {
            $rules['purpose'] = 'required|string|min:10|max:100';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Add at least one item to borrow.',
            'items.max' => 'You can borrow up to 10 different items at once.',
            'return_hours.in' => 'Return time must be between 3 and 8 hours.',
            'purpose.required' => 'Please describe why you need these items.',
            'purpose.min' => 'Purpose must be at least 10 characters.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (Auth::user()?->role !== 'student') {
                return;
            }

            $hasOverdue = Auth::user()->borrowings()
                ->where('status', 'issued')
                ->where('expected_return_date', '<', now())
                ->exists();

            if ($hasOverdue) {
                $validator->errors()->add('items', 'Return your overdue items before borrowing again.');
            }
        });

        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $quantitiesByItem = [];
            foreach ($this->input('items', []) as $index => $line) {
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
                    break;
                }
            }
        });
    }
}
