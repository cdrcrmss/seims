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
