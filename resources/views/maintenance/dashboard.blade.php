@extends('layouts.app')

@section('title', 'Maintenance Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Maintenance Dashboard</h1>
            <p class="text-gray-600">Auto-scheduling sets repair {{ \App\Services\MaintenanceAutoScheduleService::autoScheduleGraceDays() }} days ahead (damaged, needs repair, or high wear) — overdue only after that date</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('maintenance.index') }}" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                View All Records
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-up stagger-1">
        <a href="{{ route('maintenance.index', ['status' => 'upcoming']) }}" class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-yellow-500 hover:ring-yellow-300 transition-all cursor-pointer block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Scheduled</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1 font-poppins">{{ $scheduledDisplayCount }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </a>
        <a href="{{ route('maintenance.index', ['status' => 'overdue']) }}" class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-red-500 hover:ring-red-300 transition-all cursor-pointer block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Overdue</p>
                    <p class="text-3xl font-bold text-red-600 mt-1 font-poppins">{{ $overdueCount }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
        </a>
        <a href="{{ route('analytics.maintenance-predictions', ['urgency' => 'critical']) }}" class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-orange-500 hover:ring-orange-300 transition-all cursor-pointer block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Critical Items</p>
                    <p class="text-3xl font-bold text-orange-600 mt-1 font-poppins">{{ $criticalCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">Damaged units + high wear</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
            </div>
        </a>
    </div>

    @include('maintenance.partials.report-export', ['class' => 'animate-fade-in-up stagger-2'])

    <!-- Scheduled Maintenance (critical units + other scheduled records) -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-2">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                <svg class="w-5 h-5 text-yellow-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Scheduled Maintenance</span>
            </h2>
            <a href="{{ route('maintenance.index') }}" class="text-sm font-semibold text-green-600 hover:text-green-700 shrink-0">View all records</a>
        </div>

        <div class="space-y-2">
            @foreach($criticalUnitsOverdue as $unit)
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between p-4 bg-red-50 rounded-xl ring-1 ring-red-100">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="font-medium text-gray-900">{{ $unit->item?->name ?? 'Unknown item' }}</p>
                        <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded-full bg-red-100 text-red-700">Overdue</span>
                        <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded-full bg-red-100/80 text-red-600">{{ $unit->status }}</span>
                    </div>
                    <p class="text-xs text-red-600 mt-1">{{ $unit->item?->category }} &middot; <span class="font-mono text-red-700">{{ $unit->unit_code }}</span></p>
                </div>
                @include('partials.critical-unit-actions', ['unit' => $unit])
            </div>
            @endforeach

            @foreach($overdueMaintenance as $record)
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between p-4 bg-red-50 rounded-xl ring-1 ring-red-100">
                @include('partials.maintenance-record-summary', ['record' => $record])
                @include('partials.maintenance-record-actions', ['record' => $record])
            </div>
            @endforeach

            @foreach($criticalUnitsUpcoming as $unit)
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between p-4 bg-gray-50 rounded-xl ring-1 ring-gray-100">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="font-medium text-gray-900">{{ $unit->item?->name ?? 'Unknown item' }}</p>
                        <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">{{ $unit->status }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $unit->item?->category }} &middot; <span class="font-mono text-gray-600">{{ $unit->unit_code }}</span></p>
                </div>
                @include('partials.critical-unit-actions', ['unit' => $unit])
            </div>
            @endforeach

            @foreach($upcomingMaintenance as $record)
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between p-4 bg-gray-50 rounded-xl ring-1 ring-gray-100">
                @include('partials.maintenance-record-summary', ['record' => $record])
                @include('partials.maintenance-record-actions', ['record' => $record])
            </div>
            @endforeach

            @if($criticalUnitsOverdue->isEmpty() && $criticalUnitsUpcoming->isEmpty() && $overdueMaintenance->isEmpty() && $upcomingMaintenance->isEmpty())
            <p class="text-gray-400 text-sm text-center py-8">No scheduled maintenance. The system will add records automatically when wear or unit condition requires it.</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
        <!-- Critical Items (High Wear) -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-3">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span>High Wear Only (&ge;70%)</span>
            </h2>
            @forelse($criticalItems as $item)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl mb-2 last:mb-0">
                <div>
                    <p class="font-medium text-gray-900">{{ $item->name }}</p>
                    <p class="text-xs text-gray-500">{{ $item->category }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-20 bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full bg-red-500" style="width: {{ $item->wear_level }}%"></div>
                    </div>
                    <span class="text-sm font-bold text-red-600">{{ $item->wear_level }}%</span>
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-8">No critical items. All equipment in good condition!</p>
            @endforelse
        </div>

        <!-- Recently Completed -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-3">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Recently Completed</span>
            </h2>
            @forelse($recentlyCompleted as $record)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl mb-2 last:mb-0">
                <div>
                    <p class="font-medium text-gray-900">{{ $record->item?->name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-500">{{ $record->completed_date ? $record->completed_date->format('M d, Y') : 'N/A' }} &middot; {{ $record->typeLabel() }}</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-green-100 text-green-700 rounded-lg">Completed</span>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-8">No recently completed maintenance</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
