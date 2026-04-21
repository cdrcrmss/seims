@extends('layouts.app')

@section('title', 'Operational Inbox')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Operational Inbox</h1>
            <p class="text-gray-600">Today's prioritized actions — {{ now()->format('l, M d') }}</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-red-50 rounded-xl ring-1 ring-red-200 p-4">
            <p class="text-xs font-bold text-red-600 uppercase">Overdue</p>
            <p class="text-2xl font-bold text-red-700 mt-1">{{ $stats['overdue_count'] }}</p>
        </div>
        <div class="bg-blue-50 rounded-xl ring-1 ring-blue-200 p-4">
            <p class="text-xs font-bold text-blue-600 uppercase">To Issue</p>
            <p class="text-2xl font-bold text-blue-700 mt-1">{{ $stats['pending_issuance'] }}</p>
        </div>
        <div class="bg-orange-50 rounded-xl ring-1 ring-orange-200 p-4">
            <p class="text-xs font-bold text-orange-600 uppercase">Open Tickets</p>
            <p class="text-2xl font-bold text-orange-700 mt-1">{{ $stats['open_tickets'] }}</p>
        </div>
        <div class="bg-green-50 rounded-xl ring-1 ring-green-200 p-4">
            <p class="text-xs font-bold text-green-600 uppercase">Reservations</p>
            <p class="text-2xl font-bold text-green-700 mt-1">{{ $stats['today_reservations'] }}</p>
        </div>
        <div class="bg-purple-50 rounded-xl ring-1 ring-purple-200 p-4">
            <p class="text-xs font-bold text-purple-600 uppercase">Scans Today</p>
            <p class="text-2xl font-bold text-purple-700 mt-1">{{ $stats['today_scans'] }}</p>
        </div>
        <div class="bg-yellow-50 rounded-xl ring-1 ring-yellow-200 p-4">
            <p class="text-xs font-bold text-yellow-600 uppercase">Critical Items</p>
            <p class="text-2xl font-bold text-yellow-700 mt-1">{{ $stats['items_critical'] }}</p>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column: Urgent -->
        <div class="space-y-6">
            <!-- Overdue Borrowings -->
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                        Overdue Borrowings
                    </h2>
                    <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">{{ $overdueBorrowings->count() }}</span>
                </div>
                <div class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
                    @forelse($overdueBorrowings as $borrowing)
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-red-50/30 transition-colors">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $borrowing->item?->name }}</p>
                            <p class="text-xs text-gray-500">{{ $borrowing->user?->name }} · <span class="text-red-600 font-bold">{{ $borrowing->overdue_days }}d overdue</span></p>
                        </div>
                        <a href="{{ route('staff.borrowings.index', ['status' => 'issued']) }}" class="text-xs font-semibold text-red-600 hover:text-red-800">View</a>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-400 text-sm">No overdue items</div>
                    @endforelse
                </div>
            </div>

            <!-- Urgent Maintenance -->
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                        Urgent Maintenance
                    </h2>
                    <span class="text-xs font-bold text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full">{{ $urgentMaintenance->count() }}</span>
                </div>
                <div class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
                    @forelse($urgentMaintenance as $ticket)
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-orange-50/30 transition-colors">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $ticket->item?->name }}</p>
                            <p class="text-xs text-gray-500">
                                <span class="inline-flex px-1.5 py-0.5 rounded text-xs font-bold {{ $ticket->priority === 'critical' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">{{ strtoupper($ticket->priority) }}</span>
                                · {{ $ticket->maintenance_type }} · {{ $ticket->sla_status ?? 'open' }}
                            </p>
                        </div>
                        <form method="POST" action="{{ route('maintenance.start-work', $ticket) }}">
                            @csrf @method('PATCH')
                            <button class="text-xs font-semibold text-blue-600 hover:text-blue-800">Start</button>
                        </form>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-400 text-sm">No urgent tickets</div>
                    @endforelse
                </div>
            </div>

            <!-- No-Show Candidates -->
            @if($noShowCandidates->isNotEmpty())
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                        No-Show Candidates
                    </h2>
                    <span class="text-xs font-bold text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-full">{{ $noShowCandidates->count() }}</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($noShowCandidates as $res)
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-yellow-50/30 transition-colors">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $res->room?->name }}</p>
                            <p class="text-xs text-gray-500">{{ $res->user?->name }} · {{ $res->start_datetime->format('g:ia') }}</p>
                        </div>
                        <form method="POST" action="{{ route('reservations.no-show', $res) }}">
                            @csrf @method('PATCH')
                            <button class="text-xs font-semibold text-orange-600 hover:text-orange-800">Mark No-Show</button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Schedule & Activity -->
        <div class="space-y-6">
            <!-- Pending Issuance -->
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Awaiting Pickup
                    </h2>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">{{ $pendingIssuance->count() }}</span>
                </div>
                <div class="divide-y divide-gray-50 max-h-64 overflow-y-auto">
                    @forelse($pendingIssuance as $borrowing)
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-blue-50/30 transition-colors">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $borrowing->item?->name }}</p>
                            <p class="text-xs text-gray-500">{{ $borrowing->user?->name }} · Qty: {{ $borrowing->quantity }}</p>
                        </div>
                        <form method="POST" action="{{ route('staff.borrowings.issue', $borrowing) }}">
                            @csrf @method('PATCH')
                            <button class="px-2 py-1 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Issue</button>
                        </form>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-400 text-sm">No items awaiting pickup</div>
                    @endforelse
                </div>
            </div>

            <!-- Today's Reservations -->
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        Today's Room Schedule
                    </h2>
                </div>
                <div class="divide-y divide-gray-50 max-h-64 overflow-y-auto">
                    @forelse($todayReservations as $res)
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-green-50/30 transition-colors">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $res->room?->name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $res->start_datetime->format('g:ia') }} - {{ $res->end_datetime->format('g:ia') }}
                                · {{ $res->user?->name }}
                            </p>
                        </div>
                        <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-semibold {{ $res->status === 'checked_in' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                            {{ $res->status === 'checked_in' ? 'Active' : 'Pending' }}
                        </span>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-400 text-sm">No reservations today</div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Scans -->
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        Recent Scans
                    </h2>
                    <a href="{{ route('qr.scan-history') }}" class="text-xs font-semibold text-purple-600 hover:text-purple-800">View All</a>
                </div>
                <div class="divide-y divide-gray-50 max-h-48 overflow-y-auto">
                    @forelse($todayScans as $event)
                    <div class="px-6 py-2.5 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-700">{{ str_replace('_', ' ', $event->action_type) }}</p>
                            <p class="text-xs text-gray-400">{{ $event->user?->name }} · {{ $event->created_at->format('g:ia') }}</p>
                        </div>
                        <span class="w-2 h-2 rounded-full {{ $event->outcome === 'success' ? 'bg-green-400' : ($event->outcome === 'warning' ? 'bg-yellow-400' : 'bg-red-400') }}"></span>
                    </div>
                    @empty
                    <div class="px-6 py-6 text-center text-gray-400 text-sm">No scans today</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Critical Items -->
    @if($criticalItems->isNotEmpty())
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Critical Wear Items
            </h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 p-6">
            @foreach($criticalItems as $item)
            <div class="bg-yellow-50 rounded-xl p-4 ring-1 ring-yellow-200">
                <p class="text-sm font-bold text-gray-900 truncate">{{ $item->name }}</p>
                <div class="mt-2 flex items-center gap-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full bg-red-500" style="width: {{ $item->wear_level }}%"></div>
                    </div>
                    <span class="text-xs font-bold text-red-600">{{ $item->wear_level }}%</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ ucfirst($item->status) }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
