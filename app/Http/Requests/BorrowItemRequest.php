<?php

namespace App\Http\Requests;

use App\Http\Controllers\AdminController;
use App\Models\Borrowing;
use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class BorrowItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'student';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $settings = AdminController::loadSettings();
        $maxDays = $settings['max_borrow_days'] ?? 7;

        return [
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:10',
            ],
            'expected_return_date' => [
                'required',
                'date',
                'after:today',
                'before_or_equal:' . now()->addDays($maxDays)->toDateString(),
            ],
            'purpose' => [
                'required',
                'string',
                'min:10',
                'max:100',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        $settings = AdminController::loadSettings();
        $maxDays = $settings['max_borrow_days'] ?? 7;

        return [
            'item_id.required' => 'Please select an item to borrow.',
            'item_id.exists' => 'The selected item does not exist.',
            'quantity.required' => 'Please specify the quantity.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'You cannot borrow more than 10 of the same item at once.',
            'expected_return_date.required' => 'Please specify when you will return the item.',
            'expected_return_date.after' => 'Return date must be after today.',
            'expected_return_date.before_or_equal' => "Return date cannot exceed {$maxDays} days from today.",
            'purpose.required' => 'Please provide the purpose for borrowing this item.',
            'purpose.min' => 'Purpose must be at least 10 characters long.',
            'purpose.max' => 'Purpose cannot exceed 100 characters.',
            'notes.max' => 'Notes cannot exceed 500 characters.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $userId = Auth::id();
            $itemId = $this->input('item_id');
            $quantity = (int) $this->input('quantity');

            // Check if item exists and has stock
            $item = Item::find($itemId);
            if (!$item) {
                $validator->errors()->add('item_id', 'Item not found.');
                return;
            }

            if ($item->available_stock < $quantity) {
                $validator->errors()->add('quantity', "Only {$item->available_stock} unit(s) available for this item.");
                return;
            }

            // Check for duplicate pending request
            $existingPending = Borrowing::where('user_id', $userId)
                ->where('item_id', $itemId)
                ->where('status', 'pending')
                ->exists();

            if ($existingPending) {
                $validator->errors()->add('item_id', 'You already have a pending request for this item.');
                return;
            }

            // Check for overdue items - block borrowing if student has overdue
            $hasOverdue = Borrowing::where('user_id', $userId)
                ->where('status', 'issued')
                ->where('expected_return_date', '<', now())
                ->exists();

            if ($hasOverdue) {
                $validator->errors()->add('item_id', 'You have overdue items. Please return them before making new requests.');
                return;
            }
        });
    }

    /**
     * Handle a passed validation attempt — enforce rate limiting.
     */
    protected function passedValidation(): void
    {
        $key = 'borrow-request:' . Auth::id();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'rate_limit' => "You are submitting requests too quickly. Please wait {$seconds} seconds before trying again.",
            ]);
        }

        RateLimiter::hit($key, 300); // 3 attempts per 5 minutes
    }
}
