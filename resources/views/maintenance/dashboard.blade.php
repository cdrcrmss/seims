@extends('layouts.app')

@section('title', 'Maintenance Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Maintenance Dashboard</h1>
            <p class="text-gray-600">Predictive maintenance analytics & condition monitoring</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('maintenance.index') }}" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                View All Records
            </a>
            <form method="POST" action="{{ route('maintenance.generate-alerts') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Generate Predictive Alerts
                </button>
            </form>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 animate-fade-in-up stagger-1">
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Upcoming</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1 font-poppins">{{ $upcomingMaintenance->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Overdue</p>
                    <p class="text-3xl font-bold text-red-600 mt-1 font-poppins">{{ $overdueMaintenance->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Critical Items</p>
                    <p class="text-3xl font-bold text-orange-600 mt-1 font-poppins">{{ $criticalItems->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Costs (YTD)</p>
                    <p class="text-3xl font-bold text-green-600 mt-1 font-poppins">₱{{ number_format($maintenanceCosts, 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Overdue Maintenance -->
        @if($overdueMaintenance->count() > 0)
        <div class="bg-white rounded-2xl ring-1 ring-red-200 shadow-sm p-6 animate-fade-in-up stagger-2">
            <h2 class="text-lg font-semibold text-red-700 mb-4 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span>Overdue Maintenance</span>
            </h2>
            <div class="space-y-3">
                @foreach($overdueMaintenance as $record)
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl">
                    <div>
                        <p class="font-medium text-gray-900">{{ $record->item?->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-red-600">Due: {{ $record->scheduled_date->format('M d, Y') }} ({{ $record->scheduled_date->diffForHumans() }})</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-red-100 text-red-700 rounded-lg">{{ ucfirst($record->maintenance_type) }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Upcoming Maintenance -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-2">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Upcoming Maintenance (Next 30 Days)</span>
            </h2>
            @forelse($upcomingMaintenance as $record)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl mb-2">
                <div>
                    <p class="font-medium text-gray-900">{{ $record->item?->name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-500">{{ $record->scheduled_date->format('M d, Y') }} · {{ ucfirst($record->maintenance_type) }}</p>
                </div>
                <span class="text-xs text-gray-500">{{ $record->scheduled_date->diffForHumans() }}</span>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-4">No upcoming maintenance scheduled</p>
            @endforelse
        </div>

        <!-- Critical Items (High Wear) -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-3">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span>Critical Wear Items (≥80%)</span>
            </h2>
            @forelse($criticalItems as $item)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl mb-2">
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
            <p class="text-gray-400 text-sm text-center py-4">No critical items. All equipment in good condition!</p>
            @endforelse
        </div>

        <!-- Recently Completed -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-3">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Recently Completed</span>
            </h2>
            @forelse($recentlyCompleted as $record)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl mb-2">
                <div>
                    <p class="font-medium text-gray-900">{{ $record->item?->name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-500">{{ $record->completed_date ? $record->completed_date->format('M d, Y') : 'N/A' }} · {{ ucfirst($record->maintenance_type) }}</p>
                </div>
                @if($record->cost)
                <span class="text-sm font-semibold text-gray-700">₱{{ number_format($record->cost, 0) }}</span>
                @endif
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-4">No recently completed maintenance</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
