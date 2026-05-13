@extends('layouts.app')

@section('title', 'Calendar')

@section('content')
<div class="space-y-6" x-data="{ 
    currentDate: new Date(new Date().toLocaleString('en-US', { timeZone: 'Asia/Manila' })),
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
        
        for (let i = 0; i < startPadding; i++) {
            days.push({ day: null, date: null });
        }
        
        for (let i = 1; i <= lastDay.getDate(); i++) {
            days.push({ day: i, date: this.currentYear + '-' + String(this.currentMonth + 1).padStart(2, '0') + '-' + String(i).padStart(2, '0') });
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
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-poppins">Calendar</h1>
            <p class="text-sm text-gray-500">View scheduled reservations</p>
        </div>
    </div>

    <!-- Calendar Card -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6">
        <!-- Month Navigation -->
        <div class="flex items-center justify-between mb-6">
            <button @click="prevMonth()" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <h2 class="text-xl font-bold text-gray-900 font-poppins" x-text="monthName"></h2>
            <button @click="nextMonth()" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

        <!-- Days of Week Header -->
        <div class="grid grid-cols-7 gap-1 mb-2">
            <div class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wide py-2">Sun</div>
            <div class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wide py-2">Mon</div>
            <div class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wide py-2">Tue</div>
            <div class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wide py-2">Wed</div>
            <div class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wide py-2">Thu</div>
            <div class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wide py-2">Fri</div>
            <div class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wide py-2">Sat</div>
        </div>

        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 gap-1">
            <template x-for="day in daysInMonth" :key="day.date || day.day">
                <div x-show="day.day" 
                     :class="getReservationsForDate(day.date).length > 0 ? 'bg-green-50 hover:bg-green-100' : 'hover:bg-gray-50'"
                     class="min-h-24 p-2 rounded-lg border border-gray-100 transition-colors cursor-pointer">
                    <div class="text-sm font-medium text-gray-900 mb-1" x-text="day.day"></div>
                    <div class="space-y-1">
                        <template x-for="res in getReservationsForDate(day.date)" :key="res.id">
                            <div class="text-xs p-1 rounded bg-green-100 text-green-800 truncate" 
                                 :title="res.title + ' - ' + res.user"
                                 x-text="res.title"></div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- Legend -->
        <div class="flex items-center gap-4 mt-6 pt-4 border-t border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-green-100 rounded"></div>
                <span class="text-xs text-gray-600">Scheduled Reservation</span>
            </div>
        </div>
    </div>
</div>
@endsection
