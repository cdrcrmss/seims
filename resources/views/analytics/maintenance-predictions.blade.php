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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-up stagger-1">
        @php
            $criticalWear = collect($allPredictions)->where('prediction.urgency', 'critical')->count();
            $critical = $criticalWear + $criticalUnits->count();
            $high = collect($allPredictions)->where('prediction.urgency', 'high')->count();
            $moderate = collect($allPredictions)->where('prediction.urgency', 'moderate')->count();
        @endphp
        <a href="{{ $urgencyFilter === 'critical' ? route('analytics.maintenance-predictions') : route('analytics.maintenance-predictions', ['urgency' => 'critical']) }}"
           class="bg-white rounded-2xl ring-1 p-6 border-l-4 border-red-500 block transition-all cursor-pointer
                  {{ $urgencyFilter === 'critical' ? 'ring-red-400 ring-2 shadow-md' : 'ring-red-200 hover:ring-red-300' }}">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Critical</p>
            <p class="text-3xl font-bold text-red-600 mt-1 font-poppins">{{ $critical }}</p>
            <p class="text-xs text-gray-500 mt-1">Damaged units + wear &ge; 70%</p>
        </a>
        <a href="{{ $urgencyFilter === 'high' ? route('analytics.maintenance-predictions') : route('analytics.maintenance-predictions', ['urgency' => 'high']) }}"
           class="bg-white rounded-2xl ring-1 p-6 border-l-4 border-orange-500 block transition-all cursor-pointer
                  {{ $urgencyFilter === 'high' ? 'ring-orange-400 ring-2 shadow-md' : 'ring-orange-200 hover:ring-orange-300' }}">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">High</p>
            <p class="text-3xl font-bold text-orange-600 mt-1 font-poppins">{{ $high }}</p>
            <p class="text-xs text-gray-500 mt-1">Wear 50-70% — Schedule soon</p>
        </a>
        <a href="{{ $urgencyFilter === 'moderate' ? route('analytics.maintenance-predictions') : route('analytics.maintenance-predictions', ['urgency' => 'moderate']) }}"
           class="bg-white rounded-2xl ring-1 p-6 border-l-4 border-yellow-500 block transition-all cursor-pointer
                  {{ $urgencyFilter === 'moderate' ? 'ring-yellow-400 ring-2 shadow-md' : 'ring-yellow-200 hover:ring-yellow-300' }}">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Moderate</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1 font-poppins">{{ $moderate }}</p>
            <p class="text-xs text-gray-500 mt-1">Wear 30-50% — Monitor closely</p>
        </a>
    </div>

    @if($urgencyFilter)
    <div class="flex items-center justify-between gap-3 animate-fade-in-up">
        <p class="text-sm text-gray-600">
            Showing <span class="font-semibold text-gray-900">{{ ucfirst($urgencyFilter) }}</span> urgency
            @if($urgencyFilter === 'critical' && $criticalUnits->count() > 0)
                ({{ $criticalUnits->count() }} {{ Str::plural('unit', $criticalUnits->count()) }}@if(count($predictions) > 0), plus {{ count($predictions) }} wear-based @endif)
            @else
                ({{ count($predictions) }} {{ Str::plural('item', count($predictions)) }})
            @endif
        </p>
        <a href="{{ route('analytics.maintenance-predictions') }}" class="text-sm font-semibold text-green-600 hover:text-green-700">Clear filter</a>
    </div>
    @else
        @php $lowUrgencyCount = collect($allPredictions)->where('prediction.urgency', 'low')->count(); @endphp
        @if($lowUrgencyCount > 0)
            <p class="text-center text-sm text-gray-500">{{ $lowUrgencyCount }} additional item(s) with routine/low urgency (scroll the list below).</p>
        @endif
    @endif


    @if($criticalUnits->count() > 0 && (!$urgencyFilter || $urgencyFilter === 'critical'))
    <div class="space-y-4 animate-fade-in-up stagger-2">
        <h2 class="text-lg font-semibold text-red-700">Damaged units (immediate critical)</h2>
        @foreach($criticalUnits as $unit)
        <div class="bg-white rounded-2xl ring-1 ring-red-200 shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $unit->item?->name ?? 'Unknown' }}</h3>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-red-100 text-red-700">Critical</span>
                    </div>
                    <p class="text-sm text-gray-600">{{ $unit->item?->category }}</p>
                    <p class="text-xs text-gray-500 mt-1">Unit ID: <span class="font-mono font-semibold text-red-700">{{ $unit->unit_code }}</span> — {{ ucfirst($unit->status) }}. Other stock units are unaffected.</p>
                </div>
                @include('partials.critical-unit-actions', ['unit' => $unit])
            </div>
        </div>
        @endforeach
    </div>
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
            @if($urgencyFilter === 'critical' && $criticalUnits->count() > 0)
            @else
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-12 text-center text-gray-400">
            @if($urgencyFilter)
                <p class="text-lg">No {{ $urgencyFilter }} urgency items</p>
                <a href="{{ route('analytics.maintenance-predictions') }}" class="text-sm text-green-600 font-semibold mt-2 inline-block">View all</a>
            @else
                <svg class="w-16 h-16 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <p class="text-lg">All equipment is in good condition</p>
                <p class="text-sm mt-1">No urgent maintenance predictions at this time</p>
            @endif
        </div>
            @endif
        @endforelse
    </div>
</div>
@endsection
