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
                            <div class="peer-checked:ring-2 peer-checked:ring-green-500 peer-checked:bg-green-50 bg-gray-50 rounded-xl p-4 transition-all hover:bg-gray-100">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-gray-900">{{ $room->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $room->building }}@if($room->floor), {{ $room->floor }}@endif</p>
                                        <div class="flex items-center gap-2 mt-1.5">
                                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                {{ $room->capacity }} seats
                                            </span>
                                            @if($room->type)
                                            <span class="text-[10px] font-semibold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full">{{ ucfirst($room->type) }}</span>
                                            @endif
                                        </div>
                                    </div>
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
                        <input type="datetime-local" name="start_datetime" id="start_datetime" x-model="startDatetime" value="{{ old('start_datetime') }}" min="{{ now()->format('Y-m-d') }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" required>
                        @error('start_datetime') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="end_datetime" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">End Date & Time</label>
                        <input type="datetime-local" name="end_datetime" id="end_datetime" x-model="endDatetime" value="{{ old('end_datetime') }}" min="{{ now()->format('Y-m-d') }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" required>
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

                <!-- Additional Notes -->
                <div>
                    <label for="notes" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                        Additional Notes <span class="font-normal normal-case text-gray-400">(optional)</span>
                    </label>
                    <textarea name="notes" id="notes" rows="2"
                              placeholder="e.g. Need projector setup, expecting 20 students, special equipment required..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all resize-none">{{ old('notes') }}</textarea>
                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Submit -->
                <div class="flex gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" :disabled="submitting"
                            :class="submitting ? 'bg-green-400 cursor-not-allowed' : (purpose.length < 10 ? 'bg-green-500 hover:bg-green-600' : 'bg-green-600 hover:bg-green-700 shadow-lg hover:shadow-xl')"
                            class="flex-1 inline-flex items-center justify-center gap-2 text-white px-6 py-4 rounded-xl text-base font-bold transition-all duration-200">
                        <template x-if="!submitting">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </template>
                        <template x-if="submitting">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </template>
                        <span x-text="submitting ? 'Submitting Request...' : 'Submit Reservation Request'"></span>
                    </button>
                    <a href="{{ route('reservations.index') }}" class="inline-flex items-center justify-center px-6 py-4 text-sm font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition-all duration-200">
                        Cancel
                    </a>
                </div>

                <!-- Submission Note -->
                <p class="text-xs text-gray-400 text-center -mt-2">
                    Your reservation will be reviewed and approved by staff. You'll receive a notification once it's processed.
                </p>
            </form>
        </div>

        <!-- Sidebar Info -->
        <div class="lg:col-span-1 space-y-4 animate-fade-in-up stagger-2">
            <!-- Active Reservations -->
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Your Reservations</p>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex items-end justify-between mb-3">
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-bold text-gray-900">{{ $activeReservationCount }}</span>
                            <span class="text-sm text-gray-400 font-medium">/ 5</span>
                        </div>
                        <span class="text-xs font-medium {{ $activeReservationCount >= 5 ? 'text-red-500' : 'text-green-600' }}">{{ 5 - $activeReservationCount }} slot{{ (5 - $activeReservationCount) !== 1 ? 's' : '' }} remaining</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all {{ $activeReservationCount >= 5 ? 'bg-red-500' : ($activeReservationCount >= 3 ? 'bg-amber-500' : 'bg-green-500') }}" style="width: {{ min(($activeReservationCount / 5) * 100, 100) }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Guidelines -->
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">How It Works</p>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0 text-white text-xs font-bold">1</span>
                        <span class="text-sm text-gray-700">Select a room and pick your date/time</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0 text-white text-xs font-bold">2</span>
                        <span class="text-sm text-gray-700">Check availability to avoid conflicts</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0 text-white text-xs font-bold">3</span>
                        @if(in_array(auth()->user()->role, ['staff', 'admin']))
                            <span class="text-sm text-gray-700">Submit and your reservation is confirmed instantly</span>
                        @else
                            <span class="text-sm text-gray-700">Submit and wait for staff approval</span>
                        @endif
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0 text-white text-xs font-bold">4</span>
                        <span class="text-sm text-gray-700">Use the room during your reserved time</span>
                    </div>
                    <div class="pt-3 border-t border-gray-100 space-y-2">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Rules</p>
                        <p class="text-xs text-gray-500">- Max 8 hours per session</p>
                        <p class="text-xs text-gray-500">- Book up to 30 days in advance</p>
                        <p class="text-xs text-gray-500">- Up to 5 active reservations</p>
                    </div>
                </div>
            </div>

            <!-- Tip -->
            <div class="bg-blue-50 rounded-2xl ring-1 ring-blue-100 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-blue-900 mb-1">Need Equipment?</p>
                            <p class="text-sm text-blue-700 leading-relaxed">Use the <a href="{{ route('student.borrow.form') }}" class="underline font-semibold">Borrow Items</a> page to request laboratory equipment separately.</p>
                        </div>
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
        purpose: {!! json_encode(old('purpose', '')) !!},
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
            if (this.submitting) {
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
