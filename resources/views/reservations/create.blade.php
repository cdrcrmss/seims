@extends('layouts.app')

@section('title', 'New Room Reservation')

@section('content')
<div class="space-y-8" x-data="reservationForm()">
    <!-- Header -->
    <div class="flex items-center space-x-4 animate-fade-in-up">
        <a href="{{ route('reservations.index') }}" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Reserve a Room</h1>
            <p class="text-gray-600">Book a laboratory or classroom for your activities</p>
        </div>
    </div>

    <!-- Errors -->
    @if($errors->has('rate_limit'))
        <div class="max-w-4xl bg-red-50 ring-1 ring-red-200 text-red-700 rounded-xl p-4 text-sm flex items-center gap-3 animate-fade-in-up">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $errors->first('rate_limit') }}
        </div>
    @endif

    @if($errors->has('conflict'))
        <div class="max-w-4xl bg-orange-50 ring-1 ring-orange-200 text-orange-700 rounded-xl p-4 text-sm flex items-center gap-3 animate-fade-in-up">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            {{ $errors->first('conflict') }}
        </div>
    @endif

    @if($errors->has('limit'))
        <div class="max-w-4xl bg-yellow-50 ring-1 ring-yellow-200 text-yellow-700 rounded-xl p-4 text-sm flex items-center gap-3 animate-fade-in-up">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $errors->first('limit') }}
        </div>
    @endif

    <div class="max-w-4xl grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('reservations.store') }}" @submit="handleSubmit($event)" class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-8 space-y-6 animate-fade-in-up stagger-1">
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
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="start_datetime" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Start Date & Time</label>
                        <input type="datetime-local" name="start_datetime" id="start_datetime" x-model="startDatetime" value="{{ old('start_datetime') }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" required>
                        @error('start_datetime') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="end_datetime" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">End Date & Time</label>
                        <input type="datetime-local" name="end_datetime" id="end_datetime" x-model="endDatetime" value="{{ old('end_datetime') }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" required>
                        @error('end_datetime') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Duration indicator -->
                <div x-show="startDatetime && endDatetime" x-transition class="text-xs text-gray-500 -mt-2">
                    Duration: <span x-text="computeDuration()" class="font-medium text-gray-700"></span>
                    <span x-show="durationHours > 8" class="text-red-500 font-medium ml-1">(max 8 hours per session)</span>
                </div>

                <!-- Availability Check Result -->
                <div x-show="availabilityChecked" x-transition>
                    <div :class="available ? 'bg-green-50 ring-1 ring-green-200 text-green-700' : 'bg-red-50 ring-1 ring-red-200 text-red-700'" class="rounded-xl p-4 text-sm flex items-center space-x-3">
                        <template x-if="available">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </template>
                        <template x-if="!available">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </template>
                        <span x-text="available ? 'This room is available for the selected time!' : 'Conflict detected! This room is already reserved for the selected time.'"></span>
                    </div>
                </div>

                <!-- Check Availability Button -->
                <button type="button" @click="checkAvailability()" :disabled="!canCheckAvailability()" 
                        :class="canCheckAvailability() ? 'bg-gray-100 hover:bg-gray-200 text-gray-700' : 'bg-gray-50 text-gray-400 cursor-not-allowed'"
                        class="w-full px-4 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Check Availability</span>
                </button>

                <!-- Purpose -->
                <div>
                    <label for="purpose" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                        Purpose <span class="font-normal normal-case text-gray-400">(min 10 characters)</span>
                    </label>
                    <textarea name="purpose" id="purpose" rows="3" x-model="purpose"
                              placeholder="e.g. Physics Lab experiment for BSIT 2A, Chemistry practical session..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all resize-none" required>{{ old('purpose') }}</textarea>
                    <div class="flex justify-between mt-1">
                        @error('purpose') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        <span class="text-xs ml-auto" :class="purpose.length >= 10 ? 'text-green-600' : 'text-gray-400'" x-text="purpose.length + '/500'"></span>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex flex-col-reverse sm:flex-row gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('reservations.index') }}" class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold text-gray-700 bg-white ring-1 ring-gray-200 hover:bg-gray-50 rounded-xl transition-all duration-200">
                        Cancel
                    </a>
                    <button type="submit" :disabled="submitting || purpose.length < 10"
                            :class="(submitting || purpose.length < 10) ? 'bg-green-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 shadow-sm hover:shadow-md'"
                            class="flex-1 inline-flex items-center justify-center gap-2 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-200">
                        <template x-if="!submitting">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </template>
                        <template x-if="submitting">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </template>
                        <span x-text="submitting ? 'Submitting...' : 'Reserve Room'"></span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar Info -->
        <div class="lg:col-span-1 space-y-4 animate-fade-in-up stagger-2">
            <!-- Active Reservations -->
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-5">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Your Reservations</p>
                <div class="flex items-end justify-between mb-3">
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-bold text-gray-900">{{ $activeReservationCount }}</span>
                        <span class="text-sm text-gray-400 font-medium">/ 5</span>
                    </div>
                    <span class="text-xs font-semibold {{ $activeReservationCount >= 5 ? 'text-red-500' : 'text-green-600' }}">{{ 5 - $activeReservationCount }} slot{{ (5 - $activeReservationCount) !== 1 ? 's' : '' }} remaining</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all {{ $activeReservationCount >= 5 ? 'bg-red-500' : ($activeReservationCount >= 3 ? 'bg-amber-500' : 'bg-green-500') }}" style="width: {{ min(($activeReservationCount / 5) * 100, 100) }}%"></div>
                </div>
            </div>

            <!-- Guidelines -->
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-5">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">How It Works</p>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-green-100 text-green-700 flex items-center justify-center flex-shrink-0 text-[10px] font-bold mt-0.5">1</span>
                        <span class="text-sm text-gray-600">Select a room and pick your date/time</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-green-100 text-green-700 flex items-center justify-center flex-shrink-0 text-[10px] font-bold mt-0.5">2</span>
                        <span class="text-sm text-gray-600">Check availability to avoid conflicts</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-green-100 text-green-700 flex items-center justify-center flex-shrink-0 text-[10px] font-bold mt-0.5">3</span>
                        <span class="text-sm text-gray-600">Submit and wait for staff approval</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-green-100 text-green-700 flex items-center justify-center flex-shrink-0 text-[10px] font-bold mt-0.5">4</span>
                        <span class="text-sm text-gray-600">Use the room during your reserved time</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 space-y-1.5">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Rules</p>
                    <p class="text-xs text-gray-500">Max 8 hours per session</p>
                    <p class="text-xs text-gray-500">Book up to 30 days in advance</p>
                    <p class="text-xs text-gray-500">Up to 5 active reservations</p>
                </div>
            </div>

            <!-- Tip -->
            <div class="bg-gray-50 rounded-2xl ring-1 ring-gray-200 p-5">
                <p class="text-sm font-semibold text-gray-900 mb-1">Need Equipment?</p>
                <p class="text-xs text-gray-500 leading-relaxed">Use the <a href="{{ route('student.borrow.form') }}" class="text-green-600 font-semibold hover:underline">Borrow Items</a> page to request laboratory equipment separately.</p>
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
