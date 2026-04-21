@extends('layouts.app')

@section('title', 'Reservation Report')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Reservation Report</h1>
            <p class="text-gray-600">Room utilization, no-show tracking, and scheduling trends — last {{ $days }} days</p>
        </div>
        <div class="flex gap-2 items-center">
            <form method="GET" class="flex gap-2 items-center">
                <select name="days" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5" onchange="this.form.submit()">
                    @foreach([7, 30, 60, 90] as $d)
                    <option value="{{ $d }}" {{ $days == $d ? 'selected' : '' }}>{{ $d }} days</option>
                    @endforeach
                </select>
            </form>
            <form method="POST" action="{{ route('reports.export') }}">
                @csrf
                <input type="hidden" name="type" value="reservations">
                <input type="hidden" name="days" value="{{ $days }}">
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all">Export CSV</button>
            </form>
            <a href="{{ route('reports.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">&larr; Reports</a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Total</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalReservations }}</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Completed</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $completedCount }}</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">No-Shows</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $noShowCount }}</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Cancelled</p>
            <p class="text-2xl font-bold text-gray-600 mt-1">{{ $cancelledCount }}</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">No-Show Rate</p>
            <p class="text-2xl font-bold {{ $noShowRate > 15 ? 'text-red-600' : ($noShowRate > 5 ? 'text-orange-600' : 'text-green-600') }} mt-1">{{ $noShowRate }}%</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Status Breakdown -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Status Breakdown</h2>
            @php
                $resColors = ['pending' => 'bg-yellow-500', 'approved' => 'bg-blue-500', 'checked_in' => 'bg-indigo-500', 'completed' => 'bg-green-500', 'cancelled' => 'bg-gray-400', 'rejected' => 'bg-red-400', 'no_show' => 'bg-red-600'];
            @endphp
            <div class="space-y-3">
                @foreach($statusCounts as $status => $count)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full {{ $resColors[$status] ?? 'bg-gray-400' }}"></div>
                        <span class="text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Top Rooms -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Most Reserved Rooms</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($topRooms as $i => $tr)
                <div class="px-6 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-gray-400 w-5">{{ $i + 1 }}</span>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $tr->room?->name ?? 'Deleted Room' }}</p>
                            <p class="text-xs text-gray-500">{{ $tr->room?->building }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-purple-600">{{ $tr->reservation_count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Top No-Show Users -->
    @if($topNoShows->isNotEmpty())
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Top No-Show Users</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">#</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">User</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Student ID</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">No-Shows</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($topNoShows as $i => $ns)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-gray-400 font-bold">{{ $i + 1 }}</td>
                        <td class="px-6 py-3 font-semibold text-gray-900">{{ $ns->user?->name ?? 'Deleted User' }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $ns->user?->student_id ?? '-' }}</td>
                        <td class="px-6 py-3 font-bold text-red-600">{{ $ns->no_show_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
