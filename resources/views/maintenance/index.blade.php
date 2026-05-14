@extends('layouts.app')

@section('title', 'Maintenance Records')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Maintenance Records</h1>
            <p class="text-gray-600">Track and manage equipment maintenance activities</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('maintenance.dashboard') }}" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Dashboard
            </a>
            <a href="{{ route('maintenance.create') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Schedule Maintenance
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-up stagger-1">
        <a href="{{ route('maintenance.index', ['status' => 'upcoming']) }}" class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-yellow-500 hover:ring-yellow-300 transition-all cursor-pointer block">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Upcoming</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1 font-poppins">{{ $upcomingMaintenance }}</p>
        </a>
        <a href="{{ route('maintenance.index', ['status' => 'overdue']) }}" class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-red-500 hover:ring-red-300 transition-all cursor-pointer block">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Overdue</p>
            <p class="text-3xl font-bold text-red-600 mt-1 font-poppins">{{ $overdueMaintenance }}</p>
        </a>
        <a href="{{ route('maintenance.index') }}" class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-green-500 hover:ring-green-300 transition-all cursor-pointer block">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Records</p>
            <p class="text-3xl font-bold text-green-600 mt-1 font-poppins">{{ $maintenanceRecords->total() }}</p>
        </a>
    </div>

    <!-- Generate Alerts -->
    <div class="flex justify-end animate-fade-in-up stagger-2">
        <form method="POST" action="{{ route('maintenance.generate-alerts') }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                Generate Predictive Alerts
            </button>
        </form>
    </div>

    @if($status)
    <div class="flex items-center gap-2 animate-fade-in-up stagger-2">
        <span class="text-sm text-gray-600">Filtering by:</span>
        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $status === 'upcoming' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($status) }}</span>
        <a href="{{ route('maintenance.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-red-600 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Clear filter
        </a>
    </div>
    @endif

    <!-- Records Table -->
    <div x-data="{ completeModal: false, completeId: null }">
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden animate-fade-in-up stagger-3">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Equipment</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Type</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Scheduled</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Technician</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Wear</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Status</th>
                        <th class="text-right px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($maintenanceRecords as $record)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $record->item?->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            @php
                                $typeColors = [
                                    'preventive' => 'bg-blue-50 text-blue-700',
                                    'corrective' => 'bg-orange-50 text-orange-700',
                                    'predictive' => 'bg-purple-50 text-purple-700',
                                    'routine' => 'bg-gray-100 text-gray-700',
                                    'emergency' => 'bg-red-50 text-red-700',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $typeColors[$record->maintenance_type] ?? 'bg-gray-100 text-gray-700' }}">
                                @if($record->predictive_alert_sent)
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 4l7.53 13H4.47L12 6z"/></svg>
                                @endif
                                {{ ucfirst($record->maintenance_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-700">
                            {{ $record->scheduled_date ? $record->scheduled_date->format('M d, Y') : 'N/A' }}
                            @if($record->status === 'scheduled' && $record->scheduled_date && $record->scheduled_date->isPast())
                                <span class="text-red-500 text-xs font-semibold block">OVERDUE</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $record->technician->name ?? 'Unassigned' }}</td>
                        <td class="px-6 py-4">
                            @if($record->wear_level !== null)
                            <div class="flex items-center space-x-2">
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $record->wear_level >= 70 ? 'bg-red-500' : ($record->wear_level >= 40 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $record->wear_level }}%"></div>
                                </div>
                                <span class="text-xs text-gray-600">{{ $record->wear_level }}%</span>
                            </div>
                            @else
                                <span class="text-xs text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'scheduled' => 'bg-yellow-50 text-yellow-700',
                                    'in_progress' => 'bg-blue-50 text-blue-700',
                                    'completed' => 'bg-green-50 text-green-700',
                                    'cancelled' => 'bg-red-50 text-red-700',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $statusColors[$record->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($record->status === 'scheduled')
                            <button @click="completeModal = true; completeId = {{ $record->id }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-50 text-green-700 hover:bg-green-100 ring-1 ring-green-200/60 transition-all duration-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Complete
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <p>No maintenance records found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100">
            {{ $maintenanceRecords->links() }}
        </div>
    </div>

    <!-- Complete Modal -->
    <div x-show="completeModal" x-transition x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
        <div @click.away="completeModal = false" class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Complete Maintenance</h3>
            <form :action="'/maintenance/' + completeId + '/complete'" method="POST" class="space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Completed Date</label>
                    <input type="date" name="completed_date" value="{{ now()->format('Y-m-d') }}" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Condition After</label>
                    <select name="condition_after" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                        <option value="excellent">Excellent</option>
                        <option value="good" selected>Good</option>
                        <option value="fair">Fair</option>
                        <option value="poor">Poor</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Wear Level (0-100)</label>
                    <input type="number" name="wear_level" min="0" max="100" value="20" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Actions Taken</label>
                    <textarea name="actions_taken" rows="2" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500" required></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Issues Found (optional)</label>
                    <textarea name="issues_found" rows="2" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
                </div>
                <div class="flex space-x-3 pt-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Mark Complete
                    </button>
                    <button type="button" @click="completeModal = false" class="flex-1 inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>
@endsection
