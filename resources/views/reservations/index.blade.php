@extends('layouts.app')

@section('title', 'Reservations')

@section('content')
<div class="space-y-6" x-data="{ 
    showCancelModal: false, 
    cancelId: null, 
    cancelReason: '',
    viewMode: 'list',
    currentDate: new Date(),
    reservations: {{ Js::from($reservations->map(fn($r) => [
        'id' => $r->id,
        'title' => $r->room?->name ?? 'Room',
        'start' => $r->start_datetime,
        'end' => $r->end_datetime,
        'status' => $r->status,
        'purpose' => $r->purpose,
        'user' => $r->user?->name ?? 'N/A',
    ])) }},
    get currentMonth() { return this.currentDate.getMonth(); },
    get currentYear() { return this.currentDate.getFullYear(); },
    get monthName() { return this.currentDate.toLocaleString('default', { month: 'long', year: 'numeric' }); },
    get daysInMonth() {
        const days = [];
        const firstDay = new Date(this.currentYear, this.currentMonth, 1);
        const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
        const startPadding = firstDay.getDay();
        
        // Add padding for days before month starts
        for (let i = 0; i < startPadding; i++) {
            days.push({ day: null, date: null });
        }
        
        // Add actual days
        for (let i = 1; i <= lastDay.getDate(); i++) {
            const date = new Date(this.currentYear, this.currentMonth, i);
            days.push({ day: i, date: date.toISOString().split('T')[0] });
        }
        return days;
    },
    getReservationsForDate(dateStr) {
        return this.reservations.filter(r => {
            const start = r.start.split('T')[0];
            const end = r.end.split('T')[0];
            return dateStr >= start && dateStr <= end;
        });
    },
    prevMonth() { this.currentDate = new Date(this.currentYear, this.currentMonth - 1, 1); },
    nextMonth() { this.currentDate = new Date(this.currentYear, this.currentMonth + 1, 1); }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Room Reservations</h1>
            <p class="text-gray-600">Book and manage laboratory & classroom reservations</p>
        </div>
        <div class="flex items-center space-x-3">
            <!-- View Toggle -->
            <div class="flex bg-gray-100 rounded-xl p-1">
                <button @click="viewMode = 'list'" 
                        :class="viewMode === 'list' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    <span>List</span>
                </button>
                <button @click="viewMode = 'calendar'" 
                        :class="viewMode === 'calendar' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Calendar</span>
                </button>
            </div>
            <a href="{{ route('reservations.create') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                New Reservation
            </a>
        </div>
    </div>

    <!-- Calendar View -->
    <div x-show="viewMode === 'calendar'" x-transition
         x-data="{
            get today() {
                const d = new Date();
                return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
            }
         }"
         class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">

        <!-- Calendar Header -->
        <div class="flex items-center justify-between px-6 py-4" style="background: linear-gradient(135deg, #16a34a, #059669);">
            <button @click="prevMonth()" class="p-2 rounded-lg transition-colors" style="color: white;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='transparent'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <h2 class="text-lg font-bold font-poppins" style="color: white;" x-text="monthName"></h2>
            <div class="flex items-center space-x-2">
                <button @click="currentDate = new Date()" class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all" style="background: white; color: #16a34a;">
                    Today
                </button>
                <button @click="nextMonth()" class="p-2 rounded-lg transition-colors" style="color: white;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='transparent'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>

        <!-- Day Headers -->
        <div style="display: grid; grid-template-columns: repeat(7, 1fr); background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
            <div class="py-3 text-center text-[11px] font-bold uppercase tracking-widest" style="color: #ef4444;">Sun</div>
            <div class="py-3 text-center text-[11px] font-bold uppercase tracking-widest" style="color: #6b7280;">Mon</div>
            <div class="py-3 text-center text-[11px] font-bold uppercase tracking-widest" style="color: #6b7280;">Tue</div>
            <div class="py-3 text-center text-[11px] font-bold uppercase tracking-widest" style="color: #6b7280;">Wed</div>
            <div class="py-3 text-center text-[11px] font-bold uppercase tracking-widest" style="color: #6b7280;">Thu</div>
            <div class="py-3 text-center text-[11px] font-bold uppercase tracking-widest" style="color: #6b7280;">Fri</div>
            <div class="py-3 text-center text-[11px] font-bold uppercase tracking-widest" style="color: #3b82f6;">Sat</div>
        </div>

        <!-- Calendar Grid -->
        <div style="display: grid; grid-template-columns: repeat(7, 1fr);">
            <template x-for="(cell, index) in daysInMonth" :key="index">
                <div class="min-h-[100px] p-2 transition-colors"
                     :style="'border-bottom: 1px solid #f3f4f6; border-right: 1px solid #f3f4f6;' + (cell.date === today ? 'background: #f0fdf4;' : (!cell.day ? 'background: #fafafa;' : ''))"
                     style="cursor: default;"
                     @mouseenter="if(cell.day) $el.style.background = cell.date === today ? '#dcfce7' : '#f9fafb'"
                     @mouseleave="$el.style.background = cell.date === today ? '#f0fdf4' : (!cell.day ? '#fafafa' : '')">

                    <!-- Day number -->
                    <div x-show="cell.day" class="flex items-center justify-between mb-1">
                        <span class="text-[13px] font-bold inline-flex items-center justify-center w-7 h-7 rounded-full"
                              :style="cell.date === today ? 'background: #16a34a; color: white; box-shadow: 0 1px 3px rgba(22,163,74,0.4);' : 'color: #374151;'"
                              x-text="cell.day"></span>
                        <span x-show="cell.date && getReservationsForDate(cell.date).length > 0"
                              class="text-[10px] font-bold inline-flex items-center justify-center w-5 h-5 rounded-full"
                              style="background: #dcfce7; color: #15803d;"
                              x-text="getReservationsForDate(cell.date).length"></span>
                    </div>

                    <!-- Reservation chips -->
                    <div x-show="cell.date" class="space-y-[3px]">
                        <template x-for="res in getReservationsForDate(cell.date).slice(0, 2)" :key="res.id">
                            <div class="text-[10px] leading-tight px-1.5 py-1 rounded-md truncate font-semibold"
                                 :style="
                                     res.status === 'pending' ? 'background: #fffbeb; color: #b45309; border-left: 2px solid #f59e0b;' :
                                     res.status === 'approved' ? 'background: #f0fdf4; color: #15803d; border-left: 2px solid #22c55e;' :
                                     res.status === 'cancelled' ? 'background: #fef2f2; color: #dc2626; border-left: 2px solid #ef4444;' :
                                     'background: #f9fafb; color: #6b7280; border-left: 2px solid #9ca3af;'
                                 "
                                 :title="res.title + ' — ' + res.user + ' — ' + res.purpose"
                                 x-text="res.title">
                            </div>
                        </template>
                        <div x-show="cell.date && getReservationsForDate(cell.date).length > 2"
                             class="text-[10px] font-semibold px-1.5" style="color: #9ca3af;"
                             x-text="'+' + (getReservationsForDate(cell.date).length - 2) + ' more'"></div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Legend -->
        <div class="px-6 py-3.5" style="background: #f9fafb; border-top: 1px solid #e5e7eb;">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center space-x-2 px-3 py-1.5 rounded-full" style="background: #fffbeb; border: 1px solid #fde68a;">
                    <span class="w-2 h-2 rounded-full" style="background: #f59e0b;"></span>
                    <span class="text-[11px] font-semibold" style="color: #92400e;">Pending</span>
                </div>
                <div class="flex items-center space-x-2 px-3 py-1.5 rounded-full" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                    <span class="w-2 h-2 rounded-full" style="background: #22c55e;"></span>
                    <span class="text-[11px] font-semibold" style="color: #166534;">Approved</span>
                </div>
                <div class="flex items-center space-x-2 px-3 py-1.5 rounded-full" style="background: #fef2f2; border: 1px solid #fecaca;">
                    <span class="w-2 h-2 rounded-full" style="background: #ef4444;"></span>
                    <span class="text-[11px] font-semibold" style="color: #991b1b;">Cancelled</span>
                </div>
                <div class="flex items-center space-x-2 px-3 py-1.5 rounded-full" style="background: #f3f4f6; border: 1px solid #d1d5db;">
                    <span class="w-2 h-2 rounded-full" style="background: #9ca3af;"></span>
                    <span class="text-[11px] font-semibold" style="color: #4b5563;">Completed</span>
                </div>
            </div>
        </div>
    </div>

    <!-- List View (Reservations Table) -->
    <div x-show="viewMode === 'list'" x-transition class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden animate-fade-in-up stagger-1">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Room</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Schedule</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Purpose</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Status</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Reserved By</th>
                        <th class="text-right px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($reservations as $reservation)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">
                                {{ $reservation->room?->name ?? 'N/A' }}
                            </div>
                            @if($reservation->room?->building)
                            <p class="text-xs text-gray-500">{{ $reservation->room->building }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-900 text-xs">
                                <p>{{ \Carbon\Carbon::parse($reservation->start_datetime)->format('M d, Y g:ia') }}</p>
                                <p class="text-gray-500">to {{ \Carbon\Carbon::parse($reservation->end_datetime)->format('M d, Y g:ia') }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-700 truncate max-w-[200px]">{{ $reservation->purpose }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-50 text-yellow-700',
                                    'approved' => 'bg-green-50 text-green-700',
                                    'cancelled' => 'bg-red-50 text-red-700',
                                    'completed' => 'bg-gray-100 text-gray-700',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $statusColors[$reservation->status] ?? 'bg-gray-100 text-gray-700' }}">
                                @if($reservation->conflict_detected)
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5 animate-pulse"></span>
                                @endif
                                {{ ucfirst($reservation->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $reservation->user?->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                @if(in_array(auth()->user()->role, ['staff', 'admin']) && $reservation->status === 'pending')
                                <form method="POST" action="{{ route('reservations.approve', $reservation) }}" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Approve Reservation', message: 'Are you sure you want to approve this reservation?', type: 'success' })">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-50 text-green-700 hover:bg-green-100 ring-1 ring-green-200/60 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Approve
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('reservations.reject', $reservation) }}" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Reject Reservation', message: 'Are you sure you want to reject this reservation? This action cannot be undone.', type: 'danger' })">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-50 text-red-700 hover:bg-red-100 ring-1 ring-red-200/60 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        Reject
                                    </button>
                                </form>
                                @endif

                                @if(in_array(auth()->user()->role, ['staff', 'admin']) && in_array($reservation->status, ['approved', 'checked_in']))
                                <form method="POST" action="{{ route('reservations.complete', $reservation) }}" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Complete Reservation', message: 'Mark this reservation as completed?', type: 'success' })">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 ring-1 ring-blue-200/60 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Complete
                                    </button>
                                </form>
                                @endif

                                @if(in_array(auth()->user()->role, ['staff', 'admin']) && $reservation->status === 'approved')
                                <form method="POST" action="{{ route('reservations.no-show', $reservation) }}" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Mark as No-Show', message: 'Are you sure you want to mark this student as a no-show? This action cannot be undone.', type: 'danger' })">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 ring-1 ring-amber-200/60 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        No-Show
                                    </button>
                                </form>
                                @endif

                                @if($reservation->status === 'pending' || ($reservation->status === 'approved' && (auth()->id() === $reservation->user_id || in_array(auth()->user()->role, ['staff', 'admin']))))
                                <button @click="showCancelModal = true; cancelId = {{ $reservation->id }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-50 text-red-700 hover:bg-red-100 ring-1 ring-red-200/60 transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Cancel
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p>No reservations found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100">
            {{ $reservations->links() }}
        </div>
    </div>

    <!-- Cancel Modal -->
    <div x-show="showCancelModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
        <div @click.away="showCancelModal = false" class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Cancel Reservation</h3>
            <form :action="'/reservations/' + cancelId + '/cancel'" method="POST">
                @csrf @method('PATCH')
                <textarea name="reason" x-model="cancelReason" rows="3" placeholder="Reason for cancellation (optional)" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all mb-4"></textarea>
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Confirm Cancel
                    </button>
                    <button type="button" @click="showCancelModal = false" class="flex-1 inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200">Back</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
