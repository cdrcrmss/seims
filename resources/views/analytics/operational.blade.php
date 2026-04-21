@extends('layouts.app')

@section('title', 'Operational Analytics')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Operational Analytics</h1>
            <p class="text-gray-600">SLA performance, return quality, and operational efficiency — last 90 days</p>
        </div>
        <a href="{{ route('analytics.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">&larr; Back to Analytics</a>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">MTTR</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $operationalData['mttr_hours'] ?? 'N/A' }}<span class="text-sm font-normal text-gray-400"> hrs</span></p>
            <p class="text-xs text-gray-400 mt-1">Mean Time To Repair</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Avg Resolution</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $operationalData['avg_resolution_hours'] ?? 'N/A' }}<span class="text-sm font-normal text-gray-400"> hrs</span></p>
            <p class="text-xs text-gray-400 mt-1">Ticket close time (90d)</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Open Tickets</p>
            <p class="text-2xl font-bold {{ $operationalData['open_tickets'] > 5 ? 'text-red-600' : 'text-green-600' }} mt-1">{{ $operationalData['open_tickets'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Active maintenance</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Avg Turnaround</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $operationalData['avg_turnaround_days'] ?? 'N/A' }}<span class="text-sm font-normal text-gray-400"> days</span></p>
            <p class="text-xs text-gray-400 mt-1">Issue → Return (90d)</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- No-Show Performance -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Reservation No-Show Rate</h2>
            <div class="flex items-center gap-6">
                <div class="relative w-32 h-32">
                    <svg class="w-32 h-32 -rotate-90" viewBox="0 0 36 36">
                        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                              fill="none" stroke="#e5e7eb" stroke-width="3"/>
                        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                              fill="none"
                              stroke="{{ $operationalData['no_show_rate'] > 15 ? '#ef4444' : ($operationalData['no_show_rate'] > 5 ? '#f59e0b' : '#10b981') }}"
                              stroke-width="3"
                              stroke-dasharray="{{ $operationalData['no_show_rate'] }}, 100"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-2xl font-bold {{ $operationalData['no_show_rate'] > 15 ? 'text-red-600' : ($operationalData['no_show_rate'] > 5 ? 'text-yellow-600' : 'text-green-600') }}">{{ $operationalData['no_show_rate'] }}%</span>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between gap-8">
                        <span class="text-sm text-gray-500">No-shows (90d)</span>
                        <span class="text-sm font-bold text-gray-900">{{ $operationalData['no_shows_90d'] }}</span>
                    </div>
                    <div class="flex justify-between gap-8">
                        <span class="text-sm text-gray-500">Total reservations</span>
                        <span class="text-sm font-bold text-gray-900">{{ $operationalData['total_reservations_90d'] }}</span>
                    </div>
                    <div class="mt-2 text-xs text-gray-400">
                        @if($operationalData['no_show_rate'] > 15)
                            High no-show rate. Consider stricter enforcement.
                        @elseif($operationalData['no_show_rate'] > 5)
                            Moderate. Monitor repeat offenders.
                        @else
                            Healthy no-show rate.
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Return Condition Breakdown -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Return Condition Breakdown (90d)</h2>
            @php
                $conditions = $operationalData['condition_breakdown'];
                $totalReturns = array_sum($conditions);
                $conditionColors = [
                    'good' => 'bg-green-500',
                    'fair' => 'bg-yellow-500',
                    'needs_repair' => 'bg-orange-500',
                    'damaged' => 'bg-red-500',
                ];
                $conditionLabels = [
                    'good' => 'Good',
                    'fair' => 'Fair',
                    'needs_repair' => 'Needs Repair',
                    'damaged' => 'Damaged',
                ];
            @endphp

            @if($totalReturns > 0)
            <!-- Stacked bar -->
            <div class="flex rounded-full h-6 overflow-hidden mb-4">
                @foreach($conditions as $condition => $count)
                <div class="{{ $conditionColors[$condition] ?? 'bg-gray-400' }}" style="width: {{ ($count / $totalReturns) * 100 }}%" title="{{ $conditionLabels[$condition] ?? $condition }}: {{ $count }}"></div>
                @endforeach
            </div>

            <div class="grid grid-cols-2 gap-3">
                @foreach($conditions as $condition => $count)
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full {{ $conditionColors[$condition] ?? 'bg-gray-400' }}"></div>
                    <span class="text-sm text-gray-600">{{ $conditionLabels[$condition] ?? ucfirst($condition) }}</span>
                    <span class="text-sm font-bold text-gray-900 ml-auto">{{ $count }}</span>
                    <span class="text-xs text-gray-400">({{ round(($count / $totalReturns) * 100) }}%)</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-center text-gray-400 py-8">No returns in the last 90 days.</p>
            @endif
        </div>
    </div>
</div>
@endsection
