@extends('layouts.app')

@section('title', 'New Room Reservation')

@section('content')
<div class="space-y-8" x-data="reservationForm()" x-init="init()">
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
        <div class="max-w-6xl bg-red-50 ring-1 ring-red-200 text-red-700 rounded-xl p-4 text-sm flex items-center gap-3 animate-fade-in-up">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $errors->first('rate_limit') }}
        </div>
    @endif

    @if($errors->has('conflict'))
        <div class="max-w-6xl bg-orange-50 ring-1 ring-orange-200 text-orange-700 rounded-xl p-4 text-sm flex items-center gap-3 animate-fade-in-up">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            {{ $errors->first('conflict') }}
        </div>
    @endif

    @if($errors->has('limit'))
        <div class="max-w-6xl bg-yellow-50 ring-1 ring-yellow-200 text-yellow-700 rounded-xl p-4 text-sm flex items-center gap-3 animate-fade-in-up">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $errors->first('limit') }}
        </div>
    @endif

    <div class="max-w-6xl grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('reservations.store') }}" @submit="handleSubmit($event)" class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-8 space-y-6 animate-fade-in-up stagger-1">
                @csrf
                <input type="hidden" name="reservation_type" value="room">


                <!-- Schedule (set first — drives live availability) -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Schedule</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="start_datetime" class="block text-xs font-medium text-gray-500 mb-1.5">Start Date &amp; Time</label>
                            <input type="datetime-local" name="start_datetime" id="start_datetime" x-model="startDatetime"
                                   @input="refreshRoomAvailability()" @change="refreshRoomAvailability()"
                                   value="{{ old('start_datetime') }}" min="{{ now()->format('Y-m-d\TH:i') }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-slate-400 focus:border-slate-400 transition-all" required>
                            @error('start_datetime') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="end_datetime" class="block text-xs font-medium text-gray-500 mb-1.5">End Date &amp; Time</label>
                            <input type="datetime-local" name="end_datetime" id="end_datetime" x-model="endDatetime"
                                   @input="refreshRoomAvailability()" @change="refreshRoomAvailability()"
                                   value="{{ old('end_datetime') }}" :min="startDatetime || '{{ now()->format('Y-m-d\TH:i') }}'"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-slate-400 focus:border-slate-400 transition-all" required>
                            @error('end_datetime') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div x-show="startDatetime && endDatetime" x-transition class="text-xs text-gray-500 mt-2">
                        Duration: <span x-text="computeDuration()" class="font-medium text-gray-700"></span>
                    </div>
                    <div x-show="checkingRooms" class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                        <svg class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Checking room availability…
                    </div>
                    <p x-show="scheduleReady && !checkingRooms" x-transition class="text-xs text-slate-600 mt-2">
                        Room status updated for your selected time window.
                    </p>
                </div>
                <!-- Room Selection -->
                <div>
                    <label for="room_id" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Select Room</label>
                    @if($rooms->isEmpty())
                        <div class="bg-yellow-50 ring-1 ring-yellow-200 text-yellow-700 rounded-xl p-4 text-sm">
                            No rooms are currently available. Please contact the lab staff.
                        </div>
                    @else
                    <p class="text-xs text-gray-500 mb-3" x-show="!scheduleReady">Enter start and end times above to see which rooms are available.</p>
                    <p class="text-xs text-gray-500 mb-3" x-show="scheduleReady" x-cloak>Reserved rooms are locked for your selected schedule.</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($rooms as $room)
                        <label class="block" :class="roomIsBooked({{ $room->id }}) ? 'cursor-not-allowed' : 'cursor-pointer'">
                            <input type="radio" name="room_id" value="{{ $room->id }}" x-model="selectedRoomId"
                                   :disabled="roomIsBooked({{ $room->id }})"
                                   @change="onRoomSelect()"
                                   class="peer hidden" required {{ old('room_id') == $room->id ? 'checked' : '' }}>
                            <div class="group rounded-xl p-5 transition-all h-full flex flex-col items-center justify-center text-center gap-2 border"
                                 :class="roomCardClass({{ $room->id }})">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                                     :class="roomIsBooked({{ $room->id }}) ? 'bg-red-100' : (String(selectedRoomId) === String({{ $room->id }}) ? 'bg-green-100' : 'bg-gray-100 group-hover:bg-green-50')">
                                    <svg class="w-6 h-6" :class="roomIsBooked({{ $room->id }}) ? 'text-red-600' : (String(selectedRoomId) === String({{ $room->id }}) ? 'text-green-600' : 'text-gray-500 group-hover:text-green-600')" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                                <p class="text-sm font-bold leading-tight" :class="roomIsBooked({{ $room->id }}) ? 'text-gray-500' : 'text-gray-900'">{{ $room->name }}</p>
                                <span x-show="!scheduleReady" class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">Set schedule</span>
                                <span x-show="scheduleReady && !roomIsBooked({{ $room->id }})" class="text-[10px] font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">Available</span>
                                <span x-show="scheduleReady && roomIsBooked({{ $room->id }})" class="text-[10px] font-semibold text-red-700 bg-red-100 px-2 py-0.5 rounded-full">Reserved</span>
                                <p x-show="scheduleReady && roomIsBooked({{ $room->id }})" class="text-[10px] text-red-600 leading-snug px-1" x-text="roomConflictHint({{ $room->id }})"></p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @endif
                    @error('room_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>


                <!-- Availability Check Result -->
                <div x-show="availabilityChecked" x-transition>
                    <div :class="available ? 'bg-green-50 ring-1 ring-green-200 text-green-800' : 'bg-red-50 ring-1 ring-red-200 text-red-800'" class="rounded-xl p-4 text-sm flex items-center space-x-3">
                        <template x-if="available">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </template>
                        <template x-if="!available">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </template>
                        <span x-text="availabilityMessage()"></span>
                    </div>
                </div>


                <!-- Purpose -->
                <div>
                    <label for="purpose" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                        Purpose <span class="font-normal normal-case text-gray-400">(min 10, max 100 characters)</span>
                    </label>
                    <textarea name="purpose" id="purpose" rows="3" x-model="purpose" maxlength="100"
                              placeholder="Briefly describe the purpose of your reservation..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all resize-none" required>{{ old('purpose') }}</textarea>
                    <div class="flex justify-between mt-1">
                        @error('purpose') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        <span class="text-xs ml-auto" :class="purpose.length >= 10 ? 'text-green-600' : 'text-gray-400'" x-text="purpose.length + '/100'"></span>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" :disabled="submitting || !canSubmit()"
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
        roomStatuses: {},
        scheduleReady: false,
        checkingRooms: false,
        refreshTimeout: null,

        init() {
            if (this.startDatetime && this.endDatetime) {
                this.refreshRoomAvailability();
            }
        },

        canCheckAvailability() {
            return this.selectedRoomId && this.startDatetime && this.endDatetime;
        },

        canSubmit() {
            if (!this.selectedRoomId || !this.startDatetime || !this.endDatetime) return false;
            if (this.purpose.length < 10) return false;
            if (this.roomIsBooked(this.selectedRoomId)) return false;
            return this.availabilityChecked ? this.available : !this.roomIsBooked(this.selectedRoomId);
        },

        roomIsBooked(roomId) {
            const status = this.roomStatuses[roomId];
            return this.scheduleReady && status && !status.available;
        },

        roomCardClass(roomId) {
            if (this.roomIsBooked(roomId)) {
                return 'bg-red-50 border-red-200 opacity-90 cursor-not-allowed';
            }
            if (String(this.selectedRoomId) === String(roomId)) {
                return 'bg-green-50 border-green-500 ring-2 ring-green-500';
            }
            if (!this.scheduleReady) {
                return 'bg-gray-50 border-gray-200 hover:border-green-400 hover:bg-green-50/70 peer-checked:ring-2 peer-checked:ring-green-500 peer-checked:border-green-400 peer-checked:bg-green-50';
            }
            return 'bg-gray-50 border-gray-200 hover:border-green-400 hover:bg-green-50/70 peer-checked:ring-2 peer-checked:ring-green-500 peer-checked:border-green-400 peer-checked:bg-green-50';
        },

        roomConflictHint(roomId) {
            const status = this.roomStatuses[roomId];
            if (!status || !status.conflicts || !status.conflicts.length) {
                return 'Reserved for this time slot';
            }
            const c = status.conflicts[0];
            return 'Booked ' + c.start + ' – ' + c.end;
        },

        availabilityMessage() {
            if (!this.selectedRoomId) return 'Select a room to verify availability.';
            if (this.roomIsBooked(this.selectedRoomId)) {
                return this.roomConflictHint(this.selectedRoomId) || 'This room is already reserved for your selected time.';
            }
            return this.available
                ? 'This room is available for the selected time!'
                : 'Conflict detected! This room is already reserved for the selected time.';
        },

        onRoomSelect() {
            if (this.roomIsBooked(this.selectedRoomId)) {
                this.selectedRoomId = '';
                this.available = false;
                this.availabilityChecked = false;
                return;
            }
            if (this.canCheckAvailability()) {
                const st = this.roomStatuses[this.selectedRoomId]; this.available = st ? st.available : false; this.availabilityChecked = true;
            }
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
            if (this.submitting || !this.canSubmit()) {
                e.preventDefault();
                if (this.roomIsBooked(this.selectedRoomId)) {
                    alert('That room is already reserved for your selected time. Please pick another room or change your schedule.');
                }
                return;
            }
            this.submitting = true;
        },

        refreshRoomAvailability() {
            clearTimeout(this.refreshTimeout);
            this.availabilityChecked = false;
            this.available = false;

            if (!this.startDatetime || !this.endDatetime) {
                this.scheduleReady = false;
                this.roomStatuses = {};
                if (this.selectedRoomId) this.selectedRoomId = '';
                return;
            }

            const start = new Date(this.startDatetime);
            const end = new Date(this.endDatetime);
            if (end <= start) {
                this.scheduleReady = false;
                return;
            }

            this.refreshTimeout = setTimeout(() => this.fetchAllRoomStatuses(), 350);
        },

        async fetchAllRoomStatuses() {
            this.checkingRooms = true;
            const formData = new FormData();
            formData.append('start_datetime', this.startDatetime);
            formData.append('end_datetime', this.endDatetime);
            formData.append('check_all_rooms', '1');

            try {
                const resp = await fetch('{{ route("reservations.check-availability") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: formData
                });
                const data = await resp.json();
                this.roomStatuses = data.rooms || {};
                this.scheduleReady = true;

                if (this.selectedRoomId && this.roomIsBooked(this.selectedRoomId)) {
                    this.selectedRoomId = '';
                }

                if (this.selectedRoomId) {
                    const st = this.roomStatuses[this.selectedRoomId]; this.available = st ? st.available : false; this.availabilityChecked = true;
                }
            } catch (e) {
                console.error('Room availability check failed:', e);
            } finally {
                this.checkingRooms = false;
            }
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
