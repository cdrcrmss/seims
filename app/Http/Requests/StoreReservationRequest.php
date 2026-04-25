<?php

namespace App\Http\Requests;

use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Validator;

class StoreReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'reservation_type' => 'required|in:room',
            'start_datetime'   => 'required|date|after:now',
            'end_datetime'     => 'required|date|after:start_datetime',
            'purpose'          => 'required|string|min:10|max:500',
            'room_id'          => 'required|exists:rooms,id',
            'notes'            => 'nullable|string|max:1000',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'purpose.min'            => 'Please provide a detailed purpose (at least 10 characters).',
            'purpose.required'       => 'A purpose is required for reservation requests.',
            'start_datetime.after'   => 'The reservation must start in the future.',
            'end_datetime.after'     => 'The end time must be after the start time.',
            'room_id.required'       => 'Please select a room.',
        ];
    }

    /**
     * Configure the validator instance with additional checks.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $user = Auth::user();

            // 1. Maximum duration check — reservations cannot exceed 8 hours
            $start = \Carbon\Carbon::parse($this->start_datetime);
            $end   = \Carbon\Carbon::parse($this->end_datetime);
            if ($end->diffInHours($start) > 8) {
                $validator->errors()->add('end_datetime', 'A single reservation cannot exceed 8 hours.');
                return;
            }

            // 2. No reservations more than 30 days in advance
            if ($start->diffInDays(now()) > 30) {
                $validator->errors()->add('start_datetime', 'Reservations cannot be made more than 30 days in advance.');
                return;
            }

            // 3. Maximum active (pending/approved) reservations per user: 5
            $activeCount = Reservation::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'approved'])
                ->count();

            if ($activeCount >= 5) {
                $validator->errors()->add(
                    'limit',
                    'You already have 5 active reservations. Please wait for some to be completed or cancel existing ones.'
                );
                return;
            }

            // 4. Duplicate reservation check — same room in overlapping time for this user
            $duplicateExists = Reservation::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'approved'])
                ->where('room_id', $this->room_id)
                ->where(function ($q) {
                    $q->whereBetween('start_datetime', [$this->start_datetime, $this->end_datetime])
                        ->orWhereBetween('end_datetime', [$this->start_datetime, $this->end_datetime])
                        ->orWhere(function ($q2) {
                            $q2->where('start_datetime', '<=', $this->start_datetime)
                                ->where('end_datetime', '>=', $this->end_datetime);
                        });
                })->exists();

            if ($duplicateExists) {
                $validator->errors()->add('room_id', 'You already have a reservation for this room during the selected time.');
                return;
            }

            // 5. Conflict detection — anyone's approved reservation for this room
            $roomConflict = Reservation::where('status', 'approved')
                ->where('room_id', $this->room_id)
                ->where(function ($q) {
                    $q->whereBetween('start_datetime', [$this->start_datetime, $this->end_datetime])
                        ->orWhereBetween('end_datetime', [$this->start_datetime, $this->end_datetime])
                        ->orWhere(function ($q2) {
                            $q2->where('start_datetime', '<=', $this->start_datetime)
                                ->where('end_datetime', '>=', $this->end_datetime);
                        });
                })->exists();

            if ($roomConflict) {
                $validator->errors()->add('conflict', 'The selected room is already reserved during this time period.');
                return;
            }
        });
    }

    /**
     * Handle a passed validation attempt — enforce rate limiting.
     */
    protected function passedValidation(): void
    {
        $key = 'reservation-form:' . Auth::id();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            abort(429, "You're submitting too quickly. Please wait {$seconds} seconds.");
        }

        RateLimiter::hit($key, 300); // 5 minute decay
    }
}
