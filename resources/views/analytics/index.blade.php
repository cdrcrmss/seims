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
        <form method="POST" action="{{ route('analytics.export') }}" data-file-download>
            @csrf
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-all duration-200 flex items-center space-x-2 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Export Report</span>
            </button>
        </form>
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

    <!-- Maintenance Report Export -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-100 shadow-sm overflow-hidden animate-fade-in-up stagger-2">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between cursor-pointer select-none"
             onclick="this.nextElementSibling.classList.toggle('hidden')">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-amber-500/10 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Export Maintenance Report</h3>
                    <p class="text-xs text-gray-500">Filter by date, status, and type — download as PDF or CSV</p>
                </div>
            </div>
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div class="px-6 py-5 hidden">
            <form method="POST" action="{{ route('admin.reports.maintenance-export') }}" data-file-download>
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Date From</label>
                        <input type="date" name="date_from"
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-gray-50"
                               value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Date To</label>
                        <input type="date" name="date_to"
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-gray-50"
                               value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>
                        <select name="status"
                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-gray-50">
                            <option value="all">All Statuses</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Maintenance Type</label>
                        <select name="maintenance_type"
                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-gray-50">
                            <option value="all">All Types</option>
                            <option value="preventive">Preventive</option>
                            <option value="corrective">Corrective</option>
                            <option value="predictive">Predictive</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-gray-100 rounded-xl p-1">
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg cursor-pointer has-[:checked]:bg-white has-[:checked]:shadow-sm transition-all">
                            <input type="radio" name="format" value="csv" class="sr-only" checked>
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-xs font-semibold text-gray-700">CSV</span>
                        </label>
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg cursor-pointer has-[:checked]:bg-white has-[:checked]:shadow-sm transition-all">
                            <input type="radio" name="format" value="pdf" class="sr-only">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-xs font-semibold text-gray-700">PDF</span>
                        </label>
                    </div>
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export Maintenance Report
                    </button>
                </div>
            </form>
        </div>
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

        <!-- Critical Maintenance Items -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-3">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span>Critical Maintenance (Wear ≥ 70%)</span>
            </h2>
            @forelse($dashboardData['critical_maintenance_items'] as $item)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl mb-2">
                <div>
                    <p class="font-medium text-gray-900">{{ $item->name }}</p>
                    <p class="text-xs text-gray-500">{{ $item->category }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-24 bg-gray-200 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full {{ $item->wear_level >= 80 ? 'bg-red-500' : 'bg-orange-500' }}" style="width: {{ $item->wear_level }}%"></div>
                    </div>
                    <span class="text-sm font-bold {{ $item->wear_level >= 80 ? 'text-red-600' : 'text-orange-600' }} w-14 text-right">{{ $item->wear_level }}%</span>
                </div>
            </div>
            @empty
            <div class="text-center py-6 text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <p>All equipment in good condition!</p>
            </div>
            @endforelse
            <a href="{{ route('analytics.maintenance-predictions') }}" class="block text-center text-sm text-green-600 font-semibold mt-3 hover:text-green-700">View Predictions →</a>
        </div>

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
