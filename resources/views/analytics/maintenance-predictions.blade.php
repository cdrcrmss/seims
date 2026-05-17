@extends('layouts.app')

@section('title', 'Maintenance Predictions')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center space-x-4 animate-fade-in-up">
        <a href="{{ route('analytics.index') }}" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Maintenance Predictions</h1>
            <p class="text-gray-600">AI-predicted maintenance schedules based on wear pattern analysis</p>
        </div>
    </div>

    <!-- Maintenance Report Export -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-100 shadow-sm overflow-hidden animate-fade-in-up stagger-1">
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

    <!-- Urgency Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-up stagger-1">
        @php
            $critical = collect($predictions)->where('prediction.urgency', 'critical')->count();
            $high = collect($predictions)->where('prediction.urgency', 'high')->count();
            $moderate = collect($predictions)->where('prediction.urgency', 'moderate')->count();
        @endphp
        <div class="bg-white rounded-2xl ring-1 ring-red-200 p-6 border-l-4 border-red-500">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Critical</p>
            <p class="text-3xl font-bold text-red-600 mt-1 font-poppins">{{ $critical }}</p>
            <p class="text-xs text-gray-500 mt-1">Wear ≥ 70% — Immediate attention</p>
        </div>
        <div class="bg-white rounded-2xl ring-1 ring-orange-200 p-6 border-l-4 border-orange-500">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">High</p>
            <p class="text-3xl font-bold text-orange-600 mt-1 font-poppins">{{ $high }}</p>
            <p class="text-xs text-gray-500 mt-1">Wear 50-70% — Schedule soon</p>
        </div>
        <div class="bg-white rounded-2xl ring-1 ring-yellow-200 p-6 border-l-4 border-yellow-500">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Moderate</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1 font-poppins">{{ $moderate }}</p>
            <p class="text-xs text-gray-500 mt-1">Wear 30-50% — Monitor closely</p>
        </div>
    </div>

    @php $lowUrgencyCount = collect($predictions)->where('prediction.urgency', 'low')->count(); @endphp
    @if($lowUrgencyCount > 0)
        <p class="text-center text-sm text-gray-500">{{ $lowUrgencyCount }} additional item(s) shown as routine/low urgency (scroll the list below).</p>
    @endif

    <!-- Predictions List -->
    <div class="space-y-4 animate-fade-in-up stagger-2">
        @forelse($predictions as $index => $data)
        @php
            $urgencyConfig = [
                'critical' => ['bg' => 'bg-red-50', 'ring' => 'ring-red-200', 'border' => 'border-l-4 border-red-500', 'badge' => 'bg-red-100 text-red-700', 'wear' => 'bg-red-500'],
                'high' => ['bg' => 'bg-orange-50', 'ring' => 'ring-orange-200', 'border' => 'border-l-4 border-orange-500', 'badge' => 'bg-orange-100 text-orange-700', 'wear' => 'bg-orange-500'],
                'moderate' => ['bg' => 'bg-yellow-50', 'ring' => 'ring-yellow-200', 'border' => 'border-l-4 border-yellow-500', 'badge' => 'bg-yellow-100 text-yellow-700', 'wear' => 'bg-yellow-500'],
                'low' => ['bg' => 'bg-gray-50', 'ring' => 'ring-gray-200', 'border' => 'border-l-4 border-gray-400', 'badge' => 'bg-gray-100 text-gray-600', 'wear' => 'bg-gray-400'],
            ];
            $config = $urgencyConfig[$data['prediction']['urgency']] ?? $urgencyConfig['low'];
        @endphp
        <div class="bg-white rounded-2xl ring-1 {{ $config['ring'] }} shadow-sm p-6 {{ $config['border'] }}">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $data['item']->name }}</h3>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg {{ $config['badge'] }}">
                            {{ ucfirst($data['prediction']['urgency']) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600">{{ $data['item']->category }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $data['prediction']['reasoning'] }}</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-xs text-gray-500 uppercase">Wear Level</p>
                        <div class="mt-1">
                            <div class="w-16 mx-auto bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $config['wear'] }}" style="width: {{ $data['prediction']['current_wear_level'] }}%"></div>
                            </div>
                            <p class="text-sm font-bold mt-1">{{ $data['prediction']['current_wear_level'] }}%</p>
                        </div>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500 uppercase">Wear Rate</p>
                        <p class="text-sm font-bold mt-1">{{ $data['prediction']['average_wear_rate'] }}/day</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500 uppercase">Days to Critical</p>
                        <p class="text-sm font-bold mt-1 {{ $data['prediction']['days_until_critical'] <= 7 ? 'text-red-600' : 'text-gray-900' }}">{{ $data['prediction']['days_until_critical'] }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500 uppercase">Next Maintenance</p>
                        <p class="text-sm font-bold mt-1">{{ $data['prediction']['next_maintenance_date']->format('M d') }}</p>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-12 text-center text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <p class="text-lg">All equipment is in good condition</p>
            <p class="text-sm mt-1">No urgent maintenance predictions at this time</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
