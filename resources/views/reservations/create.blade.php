@extends('layouts.app')

@section('title', 'New Room Reservation')

@section('content')
<div class="space-y-6" x-data="reservationForm()">
    <!-- Header -->
    <div class="flex items-center gap-4 animate-fade-in-up">
        <a href="{{ route('reservations.index') }}" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-poppins">Reserve a Room</h1>
            <p class="text-sm text-gray-500">Book a laboratory or classroom for your activities</p>
        </div>
    </div>

    <!-- Errors -->
    @if($errors->has('rate_limit'))
        <div class="bg-red-50 ring-1 ring-red-200 text-red-700 rounded-xl p-4 text-sm flex items-center gap-3 animate-fade-in-up">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $errors->first('rate_limit') }}
        </div>
    @endif

    @if($errors->has('conflict'))
        <div class="bg-orange-50 ring-1 ring-orange-200 text-orange-700 rounded-xl p-4 text-sm flex items-center gap-3 animate-fade-in-up">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            {{ $errors->first('conflict') }}
        </div>
    @endif

    @if($errors->has('limit'))
        <div class="bg-yellow-50 ring-1 ring-yellow-200 text-yellow-700 rounded-xl p-4 text-sm flex items-center gap-3 animate-fade-in-up">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $errors->first('limit') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <!-- Main Form (3 cols) -->
        <div class="lg:col-span-3 animate-fade-in-up stagger-1">
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-base font-bold text-gray-900 font-poppins">Room Reservation</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Select a room and schedule your booking</p>
                </div>

                <form method="POST" action="{{ route('reservations.store') }}" @submit="handleSubmit($event)" class="p-6 space-y-5">
                    @csrf
                    <input type="hidden" name="reservation_type" value="room">

                    <!-- Room Selection -->
                    <div>
                        <label for="room_id" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Select Room</label>
                        @if($rooms->isEmpty())
                            <div class="bg-yellow-50 ring-1 ring-yellow-200 text-yellow-700 rounded-xl p-4 text-sm">
                                No rooms are currently available. Please contact the lab staff.
                            </div>
                        @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($rooms as $room)
                            <label class="cursor-pointer">
                                <input type="radio" name="room_id" value="{{ $room->id }}" x-model="selectedRoomId" class="peer hidden" required {{ old('room_id') == $room->id ? 'checked' : '' }}>
                                <div class="peer-checked:ring-2 peer-checked:ring-green-500 peer-checked:bg-green-50/60 bg-gray-50 rounded-xl p-4 transition-all hover:bg-gray-100 group">
                                    <p class="text-sm font-bold text-gray-900 group-hover:text-green-700 transition-colors">{{ $room->name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $room->building }}@if($room->floor), {{ $room->floor }}@endif</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-gray-500 bg-white ring-1 ring-gray-200 px-2 py-0.5 rounded-full">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            {{ $room->capacity }} seats
                                        </span>
                                        @if($room->type)
                                        <span class="text-[10px] font-semibold text-green-700 bg-green-50 ring-1 ring-green-200 px-2 py-0.5 rounded-full">{{ ucfirst($room->type) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @endif
                        @error('room_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Schedule -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_datetime" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Start Date & Time</label>
                            <input type="datetime-local" name="start_datetime" id="start_datetime" x-model="startDatetime" value="{{ old('start_datetime') }}" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                            @error('start_datetime') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="end_datetime" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">End Date & Time</label>
                            <input type="datetime-local" name="end_datetime" id="end_datetime" x-model="endDatetime" value="{{ old('end_datetime') }}" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                            @error('end_datetime') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Duration indicator -->
                    <div x-show="startDatetime && endDatetime" x-transition class="text-xs text-gray-500 -mt-2">
                        Duration: <span x-text="computeDuration()" class="font-medium text-gray-700"></span>
                        <span x-show="durationHours > 8" class="text-red-500 font-medium ml-1">(max 8 hours per session)</span>
                    </div>

                    <!-- Check Availability Button -->
                    <button type="button" @click="checkAvailability()" :disabled="!canCheckAvailability()"
                            :class="canCheckAvailability() ? 'bg-gray-100 hover:bg-gray-200 text-gray-700' : 'bg-gray-50 text-gray-400 cursor-not-allowed'"
                            class="w-full px-4 py-2.5 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Check Availability</span>
                    </button>

                    <!-- Availability Check Result -->
                    <div x-show="availabilityChecked" x-transition>
                        <div :class="available ? 'bg-green-50 ring-1 ring-green-200 text-green-700' : 'bg-red-50 ring-1 ring-red-200 text-red-700'" class="rounded-xl p-3 text-sm flex items-center space-x-3">
                            <template x-if="available">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </template>
                            <template x-if="!available">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </template>
                            <span x-text="available ? 'This room is available for the selected time!' : 'Conflict detected! This room is already reserved for the selected time.'"></span>
                        </div>
                    </div>

                    <!-- Purpose -->
                    <div>
                        <label for="purpose" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                            Purpose <span class="font-normal normal-case text-gray-400">(min 10 characters)</span>
                        </label>
                        <textarea name="purpose" id="purpose" rows="3" x-model="purpose"
                                  placeholder="e.g. Physics Lab experiment for BSIT 2A, Chemistry practical session..."
                                  class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none" required>{{ old('purpose') }}</textarea>
                        <div class="flex justify-between mt-1">
                            @error('purpose') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                            <span class="text-xs ml-auto" :class="purpose.length >= 10 ? 'text-green-600' : 'text-gray-400'" x-text="purpose.length + '/500'"></span>
                        </div>
                    </div>

                    <!-- Info Bar -->
                    <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Active Reservations</span>
                            <div class="flex items-center gap-2">
                                <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full {{ $activeReservationCount >= 5 ? 'bg-red-500' : 'bg-green-500' }}" style="width: {{ min(($activeReservationCount / 5) * 100, 100) }}%"></div>
                                </div>
                                <span class="text-xs font-bold {{ $activeReservationCount >= 5 ? 'text-red-600' : 'text-gray-900' }}">{{ $activeReservationCount }}/5</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Max Duration</span>
                            <span class="text-xs font-bold text-gray-900">8 hours</span>
                        </div>
                        <div class="flex justify-between items-center pt-1.5 border-t border-gray-200">
                            <span class="text-xs text-gray-400 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Advance booking
                            </span>
                            <span class="text-xs text-gray-400">Up to 30 days</span>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex gap-3 pt-1">
                        <button type="submit"
                                :disabled="submitting || purpose.length < 10"
                                :class="(!submitting && purpose.length >= 10) ? 'bg-green-600 hover:bg-green-700 shadow-sm hover:shadow-md' : 'bg-gray-300 cursor-not-allowed'"
                                class="flex-1 py-3 text-white font-semibold rounded-xl text-sm transition-all duration-200 flex items-center justify-center gap-2">
                            <template x-if="!submitting">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </template>
                            <template x-if="submitting">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            </template>
                            <span x-text="submitting ? 'Submitting...' : 'Reserve Room'"></span>
                        </button>
                        <a href="{{ route('reservations.index') }}" class="inline-flex items-center justify-center px-5 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition-colors">
                            Cancel
                        </a>
                    </div>

                    <p class="text-[10px] text-gray-400 text-center leading-relaxed">
                        By submitting, you agree to use the room only during your reserved time and follow facility rules.
                    </p>
                </form>
            </div>
        </div>

        <!-- Sidebar (2 cols) -->
        <div class="lg:col-span-2 space-y-4 animate-fade-in-up stagger-2">
            <!-- How It Works -->
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm sticky top-6 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-base font-bold text-gray-900 font-poppins">How It Works</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Follow these steps to reserve a room</p>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-green-600 text-white flex items-center justify-center flex-shrink-0 text-xs font-bold">1</span>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Select a room</p>
                            <p class="text-xs text-gray-500 mt-0.5">Pick your preferred laboratory or classroom</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-green-600 text-white flex items-center justify-center flex-shrink-0 text-xs font-bold">2</span>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Set your schedule</p>
                            <p class="text-xs text-gray-500 mt-0.5">Choose date/time and check availability</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-green-600 text-white flex items-center justify-center flex-shrink-0 text-xs font-bold">3</span>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Submit for approval</p>
                            <p class="text-xs text-gray-500 mt-0.5">Staff will review and approve your booking</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-green-600 text-white flex items-center justify-center flex-shrink-0 text-xs font-bold">4</span>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Use the room</p>
                            <p class="text-xs text-gray-500 mt-0.5">Check in during your reserved time slot</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tip -->
            <div class="bg-green-50 rounded-2xl ring-1 ring-green-100 p-5">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-green-900 mb-0.5">Need Equipment?</p>
                        <p class="text-xs text-green-700 leading-relaxed">Use the <a href="{{ route('student.borrow.form') }}" class="underline font-semibold">Borrow Items</a> page to request laboratory equipment separately.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function reservationForm() {
    return {
        selectedRoomId: '{{ old("room_id", "") }}',
        startDatetime: '{{ old("start_datetime", "") }}',
        endDatetime: '{{ old("end_datetime", "") }}',
        purpose: '{{ old("purpose", "") }}',
        available: false,
        availabilityChecked: false,
        submitting: false,
        durationHours: 0,

        canCheckAvailability() {
            return this.selectedRoomId && this.startDatetime && this.endDatetime;
        },

        computeDuration() {
            if (!this.startDatetime || !this.endDatetime) return '';
            const start = new Date(this.startDatetime);
            const end = new Date(this.endDatetime);
            const diffMs = end - start;
            if (diffMs <= 0) return 'Invalid';
            const hours = Math.floor(diffMs / 3600000);
            const mins = Math.floor((diffMs % 3600000) / 60000);
            this.durationHours = diffMs / 3600000;
            if (hours > 0 && mins > 0) return hours + 'h ' + mins + 'm';
            if (hours > 0) return hours + 'h';
            return mins + 'm';
        },

        handleSubmit(e) {
            if (this.submitting || this.purpose.length < 10) {
                e.preventDefault();
                return;
            }
            this.submitting = true;
        },

        async checkAvailability() {
            if (!this.canCheckAvailability()) return;
            const formData = new FormData();
            formData.append('start_datetime', this.startDatetime);
            formData.append('end_datetime', this.endDatetime);
            formData.append('room_id', this.selectedRoomId);

            try {
                const resp = await fetch('{{ route("reservations.check-availability") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: formData
                });
                const data = await resp.json();
                this.available = data.available;
                this.availabilityChecked = true;
            } catch (e) {
                console.error('Availability check failed:', e);
            }
        }
    };
}
</script>
@endsection
