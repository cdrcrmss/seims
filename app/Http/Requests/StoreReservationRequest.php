<?php

namespace App\Http\Requests;

use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Validator;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $user = Auth::user();
        $isStaffOrAdmin = in_array($user->role, ['staff', 'admin'], true);

        return [
            'reservation_type' => 'required|in:room',
            'start_datetime'   => $isStaffOrAdmin ? 'required|date' : 'required|date|after_or_equal:now',
            'end_datetime'     => 'required|date|after:start_datetime',
            'purpose'          => 'required|string|min:10|max:100',
            'room_id'          => 'required|exists:rooms,id',
            'notes'            => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'purpose.min'            => 'Please provide a detailed purpose (at least 10 characters).',
            'purpose.max'            => 'Purpose cannot exceed 100 characters.',
            'purpose.required'       => 'A purpose is required for reservation requests.',
            'start_datetime.after'   => 'The reservation must start in the future.',
            'end_datetime.after'     => 'The end time must be after the start time.',
            'room_id.required'       => 'Please select a room.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $user = Auth::user();
            $isStaffOrAdmin = in_array($user->role, ['staff', 'admin'], true);
            $start = $this->start_datetime;
            $end = $this->end_datetime;

            if (\Carbon\Carbon::parse($start)->diffInDays(now()) > 30) {
                $validator->errors()->add('start_datetime', 'Reservations cannot be made more than 30 days in advance.');

                return;
            }

            // Slot limit applies to students only (staff/admin have no cap)
            if (! $isStaffOrAdmin) {
                $activeCount = Reservation::where('user_id', $user->id)
                    ->whereIn('status', Reservation::STUDENT_ACTIVE_STATUSES)
                    ->count();

                if ($activeCount >= 5) {
                    $validator->errors()->add(
                        'limit',
                        'You already have 5 active reservations. Please wait for some to be completed or cancel existing ones.'
                    );

                    return;
                }
            }

            $overlapFilter = function ($q) use ($start, $end) {
                $q->where('start_datetime', '<', $end)
                    ->where('end_datetime', '>', $start);
            };

            $duplicateExists = Reservation::where('user_id', $user->id)
                ->whereIn('status', Reservation::BLOCKING_STATUSES)
                ->where('room_id', $this->room_id)
                ->where($overlapFilter)
                ->exists();

            if ($duplicateExists) {
                $validator->errors()->add('room_id', 'You already have a reservation for this room during the selected time.');

                return;
            }

            $roomConflict = Reservation::whereIn('status', Reservation::BLOCKING_STATUSES)
                ->where('user_id', '!=', $user->id)
                ->where('room_id', $this->room_id)
                ->where($overlapFilter)
                ->exists();

            if ($roomConflict) {
                $validator->errors()->add('conflict', 'That room is already reserved for the time you selected. Choose another room or pick a different schedule.');
            }
        });
    }

    protected function passedValidation(): void
    {
        $key = 'reservation-form:' . Auth::id();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw \Illuminate\Validation\ValidationException::withMessages([
                'rate_limit' => "You're submitting too quickly. Please wait {$seconds} seconds.",
            ]);
        }

        RateLimiter::hit($key, 300);
    }
}
