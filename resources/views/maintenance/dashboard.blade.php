@extends('layouts.app')

@section('title', 'Maintenance Dashboard')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 font-poppins">Maintenance Dashboard</h1>
            <p class="text-slate-500">Unified maintenance queue and condition monitoring</p>
        </div
        <a href="{{ route('maintenance.index') }}" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 px-5 py-2.5 rounded-xl text-sm font-semibold ring-1 ring-slate-200 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            View All Records
        </a>
    </div

    {{-- Summary stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 animate-fade-in-up stagger-1">
        <a href="{{ route('maintenance.index', ['status' => 'upcoming']) }}" class="bg-white rounded-2xl ring-1 ring-slate-200/80 p-6 shadow-sm hover:ring-slate-300 transition-all block">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Upcoming</p>
            <p class="text-3xl font-bold text-slate-900 mt-1 font-poppins">{{ $upcomingMaintenance->count() }}</p>
            <p class="text-xs text-slate-400 mt-1">Next 30 days</p>
        </a>
        <a href="{{ route('maintenance.index', ['status' => 'overdue']) }}" class="bg-white rounded-2xl ring-1 ring-slate-200/80 p-6 shadow-sm hover:ring-rose-200 transition-all block relative overflow-hidden">
            <div class="absolute top-0 left-0 w-0.5 h-full bg-rose-400 rounded-r-full"></div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Overdue</p>
            <p class="text-3xl font-bold text-rose-700 mt-1 font-poppins">{{ $overdueMaintenance->count() }}</p>
            <p class="text-xs text-rose-600/80 mt-1">Requires attention</p>
        </a>
        <a href="{{ route('analytics.maintenance-predictions', ['urgency' => 'critical']) }}" class="bg-white rounded-2xl ring-1 ring-slate-200/80 p-6 shadow-sm hover:ring-slate-300 transition-all block">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Critical Items</p>
            <p class="text-3xl font-bold text-slate-900 mt-1 font-poppins">{{ $criticalCount }}</p>
            <p class="text-xs text-slate-400 mt-1">Damaged units + high wear</p>
        </a>
    </div

    @include('maintenance.partials.report-export', ['class' => 'animate-fade-in-up stagger-2'])

    {{-- Unified maintenance table --}}
    <div class="bg-white rounded-2xl ring-1 ring-slate-200/80 shadow-sm overflow-hidden animate-fade-in-up stagger-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-6 py-5 border-b border-slate-100">
            <div>
                <h2 class="text-lg font-semibold text-slate-900 font-poppins">Maintenance Management</h2>
                <p class="text-sm text-slate-500 mt-0.5">Critical units first, then overdue and scheduled work</p>
            </div
            <a href="{{ route('analytics.maintenance-predictions', ['urgency' => 'critical']) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 shrink-0">View analytics &rarr;</a>
        </div

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Item Name</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Category</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Asset Code</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Scheduled</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Priority</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($managementRows as $row)
                    <tr class="hover:bg-slate-50/50 transition-colors {{ $row['priority'] === 'critical' ? 'bg-rose-50/30' : '' }}">
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $row['item_name'] }}</td>
                        <td class="px-4 py-4 text-slate-600">{{ $row['category'] }}</td>
                        <td class="px-4 py-4 font-mono text-xs text-slate-700">{{ $row['asset_code'] }}</td>
                        <td class="px-4 py-4 text-slate-600">{{ $row['maintenance_type'] }}</td>
                        <td class="px-4 py-4 text-slate-600 whitespace-nowrap">
                            @if($row['scheduled_date'])
                                {{ $row['scheduled_date']->format('M d, Y') }}
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            @if($row['priority'] === 'critical')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-rose-100 text-rose-800 ring-1 ring-rose-200/60">Critical</span>
                            @elseif($row['priority'] === 'overdue')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-rose-50 text-rose-700 ring-1 ring-rose-200/50">Overdue</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wide bg-slate-100 text-slate-600">Scheduled</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                @if(!empty($row['unit']))
                                    @include('partials.critical-unit-actions', ['unit' => $row['unit']])
                                @else
                                    <a href="{{ route('maintenance.index') }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-slate-700 hover:bg-slate-50 ring-1 ring-slate-200 transition-colors whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                        Maintenance list
                                    </a>
                                @endif
                            </div
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            No maintenance tasks in queue. All equipment is in good standing.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div
    </div

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
        {{-- High wear --}}
        <div class="bg-white rounded-2xl ring-1 ring-slate-200/80 shadow-sm p-6 animate-fade-in-up stagger-3">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">High Wear Only (&ge;70%)</h2>
            @forelse($criticalItems as $item)
            <div class="flex items-center justify-between py-3 border-b border-slate-50 last:border-0">
                <div>
                    <p class="font-medium text-slate-900">{{ $item->name }}</p>
                    <p class="text-xs text-slate-500">{{ $item->category }}</p>
                </div
                <div class="flex items-center gap-3">
                    <div class="w-20 bg-slate-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full bg-slate-600" style="width: {{ $item->wear_level }}%"></div
                    </div
                    <span class="text-sm font-semibold text-slate-700 w-10 text-right">{{ $item->wear_level }}%</span>
                </div
            </div
            @empty
            <p class="text-slate-400 text-sm text-center py-8">No high-wear catalog items.</p>
            @endforelse
        </div

        {{-- Recently completed --}}
        <div class="bg-white rounded-2xl ring-1 ring-slate-200/80 shadow-sm p-6 animate-fade-in-up stagger-3">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Recently Completed</h2>
            @forelse($recentlyCompleted as $record)
            <div class="flex items-center justify-between py-3 border-b border-slate-50 last:border-0">
                <div>
                    <p class="font-medium text-slate-900">{{ $record->item?->name ?? 'Unknown' }}</p>
                    <p class="text-xs text-slate-500">{{ $record->completed_date ? $record->completed_date->format('M d, Y') : 'N/A' }} &middot; {{ ucfirst($record->maintenance_type) }}</p>
                </div
                <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg">Done</span>
            </div
            @empty
            <p class="text-slate-400 text-sm text-center py-8">No recently completed maintenance.</p>
            @endforelse
        </div
    </div
</div
@include('partials.dispose-critical-unit-script')
@endsection
