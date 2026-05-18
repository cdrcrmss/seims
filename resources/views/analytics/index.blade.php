@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Predictive Analytics</h1>
            <p class="text-gray-600">AI-powered insights for inventory management</p>
        </div>
    </div>

    <!-- Quick Nav -->
    <div class="flex flex-wrap gap-3 animate-fade-in-up stagger-1">
        <a href="{{ route('analytics.demand-forecast') }}" class="px-4 py-2 bg-white ring-1 ring-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-green-50 hover:text-green-700 hover:ring-green-200 transition-all">
            Demand Forecast
        </a>
        <a href="{{ route('analytics.utilization') }}" class="px-4 py-2 bg-white ring-1 ring-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-green-50 hover:text-green-700 hover:ring-green-200 transition-all">
            Utilization
        </a>
        <a href="{{ route('analytics.maintenance-predictions') }}" class="px-4 py-2 bg-white ring-1 ring-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-green-50 hover:text-green-700 hover:ring-green-200 transition-all">
            Maintenance
        </a>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 animate-fade-in-up stagger-2">
        <a href="{{ route('staff.items.index') }}" class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-green-500 hover:ring-green-300 transition-all cursor-pointer block">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Items</p>
            <p class="text-3xl font-bold text-green-600 mt-1 font-poppins">{{ $dashboardData['total_items'] }}</p>
        </a>
        <a href="{{ route('staff.items.index', ['stock_filter' => 'low_stock']) }}" class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-red-500 hover:ring-red-300 transition-all cursor-pointer block">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Low Stock</p>
            <p class="text-3xl font-bold text-red-600 mt-1 font-poppins">{{ $dashboardData['low_stock_items'] }}</p>
        </a>
        <a href="{{ route('maintenance.dashboard') }}" class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-orange-500 hover:ring-orange-300 transition-all cursor-pointer block">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Maintenance Due</p>
            <p class="text-3xl font-bold text-orange-600 mt-1 font-poppins">{{ $dashboardData['maintenance_due_items'] }}</p>
        </a>
        <a href="{{ route('reservations.index') }}" class="bg-white rounded-2xl ring-1 ring-gray-200 p-6 border-l-4 border-blue-500 hover:ring-blue-300 transition-all cursor-pointer block">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Active Reservations</p>
            <p class="text-3xl font-bold text-blue-600 mt-1 font-poppins">{{ $dashboardData['active_reservations'] }}</p>
        </a>
    </div>

    @include('analytics.partials.report-export', ['class' => 'animate-fade-in-up stagger-2'])

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Top Utilized Items -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-3">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                <span>Top Utilized Equipment (30 Days)</span>
            </h2>
            @forelse($dashboardData['top_utilized_items'] as $entry)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl mb-2">
                <div>
                    <p class="font-medium text-gray-900">{{ $entry['item']->name }}</p>
                    <p class="text-xs text-gray-500">{{ $entry['item']->category }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-24 bg-gray-200 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full bg-blue-500" style="width: {{ min($entry['utilization_rate'], 100) }}%"></div>
                    </div>
                    <span class="text-sm font-bold text-gray-700 w-14 text-right">{{ number_format($entry['utilization_rate'], 1) }}%</span>
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-4">No utilization data available</p>
            @endforelse
            <a href="{{ route('analytics.utilization') }}" class="block text-center text-sm text-green-600 font-semibold mt-3 hover:text-green-700">View All →</a>
        </div>

        @include('analytics.partials.critical-maintenance-widget')

        <!-- Monthly Borrowing Trends -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Monthly Borrowing Trends ({{ now()->year }})</h2>
            <div class="space-y-2">
                @php $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; @endphp
                @foreach($monthlyBorrowings as $data)
                <div class="flex items-center space-x-3">
                    <span class="text-xs text-gray-500 w-8">{{ $months[($data->month ?? 1) - 1] ?? 'N/A' }}</span>
                    <div class="flex-1 bg-gray-100 rounded-full h-4">
                        <div class="h-4 bg-green-500 rounded-full flex items-center justify-end pr-2" style="width: {{ min(($data->count / max($monthlyBorrowings->max('count'), 1)) * 100, 100) }}%">
                            @if($data->count > 0)
                            <span class="text-[10px] text-white font-bold">{{ $data->count }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
