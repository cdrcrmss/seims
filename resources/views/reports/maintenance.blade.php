@extends('layouts.app')

@section('title', 'Maintenance Report')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Maintenance Report</h1>
            <p class="text-gray-600">SLA compliance, costs, and ticket analysis</p>
        </div>
        <div class="flex gap-2 items-center">
            <form method="POST" action="{{ route('reports.export') }}">
                @csrf
                <input type="hidden" name="type" value="maintenance">
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all">Export CSV</button>
            </form>
            <a href="{{ route('reports.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">&larr; Reports</a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">MTTR</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $mttr ?? 'N/A' }}<span class="text-sm font-normal text-gray-400"> hrs</span></p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Total Cost ({{ $days }}d)</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">₱{{ number_format($totalCost ?? 0, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Avg Cost</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">₱{{ number_format($avgCost ?? 0, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Total Tickets</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ array_sum($slaCounts) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- SLA Status -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">SLA Status</h2>
            @php
                $slaColors = ['open' => 'bg-red-500', 'in_progress' => 'bg-blue-500', 'waiting_parts' => 'bg-yellow-500', 'completed' => 'bg-green-500', 'verified' => 'bg-purple-500'];
            @endphp
            <div class="space-y-3">
                @foreach($slaCounts as $status => $count)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full {{ $slaColors[$status] ?? 'bg-gray-400' }}"></div>
                        <span class="text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Priority Distribution -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Priority</h2>
            @php
                $prioColors = ['critical' => 'bg-red-500', 'high' => 'bg-orange-500', 'normal' => 'bg-blue-500', 'low' => 'bg-gray-400'];
            @endphp
            <div class="space-y-3">
                @foreach($priorityCounts as $prio => $count)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full {{ $prioColors[$prio] ?? 'bg-gray-400' }}"></div>
                        <span class="text-sm text-gray-700">{{ ucfirst($prio) }}</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Trigger Sources -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Trigger Source</h2>
            @php
                $triggerLabels = ['return_inspection' => 'Return Inspection', 'predictive_alert' => 'Predictive Alert', 'usage_threshold' => 'Usage Threshold', 'manual' => 'Manual'];
            @endphp
            <div class="space-y-3">
                @forelse($triggerCounts as $trigger => $count)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">{{ $triggerLabels[$trigger] ?? ucfirst(str_replace('_', ' ', $trigger)) }}</span>
                    <span class="text-sm font-bold text-gray-900">{{ $count }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-400">No auto-generated tickets yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Frequent Items -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Items with Most Maintenance Tickets</h2>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($frequentItems as $fi)
            <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50">
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ $fi->item?->name ?? 'Deleted Item' }}</p>
                    <p class="text-xs text-gray-500">{{ $fi->item?->category }}</p>
                </div>
                <span class="text-sm font-bold text-orange-600">{{ $fi->ticket_count }} tickets</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
