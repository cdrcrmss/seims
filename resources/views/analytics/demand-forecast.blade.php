@extends('layouts.app')

@section('title', 'Demand Forecast')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center space-x-4 animate-fade-in-up">
        <a href="{{ route('analytics.index') }}" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Demand Forecast</h1>
            <p class="text-gray-600">Predicted demand based on historical borrowing patterns (next 30 days)</p>
        </div>
    </div>

    <!-- Forecast Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($forecasts as $index => $data)
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up" style="animation-delay: {{ $index * 0.05 }}s">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $data['item']->name }}</h3>
                    <p class="text-xs text-gray-500">{{ $data['item']->category }}</p>
                </div>
                @php
                    $confColors = ['high' => 'bg-green-50 text-green-700', 'medium' => 'bg-yellow-50 text-yellow-700', 'low' => 'bg-gray-100 text-gray-500'];
                @endphp
                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg {{ $confColors[$data['forecast']['confidence']] ?? 'bg-gray-100 text-gray-500' }}">
                    {{ ucfirst($data['forecast']['confidence']) }} confidence
                </span>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 uppercase">Predicted Demand</p>
                    <p class="text-xl font-bold text-green-600">{{ $data['forecast']['predicted_demand'] }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 uppercase">Avg Daily</p>
                    <p class="text-xl font-bold text-gray-900">{{ $data['forecast']['average_daily_demand'] }}</p>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">Trend:</span>
                    @if($data['forecast']['trend'] === 'increasing')
                        <span class="flex items-center text-green-600 text-sm font-semibold">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            Increasing
                        </span>
                    @elseif($data['forecast']['trend'] === 'decreasing')
                        <span class="flex items-center text-red-600 text-sm font-semibold">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                            Decreasing
                        </span>
                    @else
                        <span class="text-gray-500 text-sm font-semibold">→ Stable</span>
                    @endif
                </div>
                <span class="text-xs text-gray-400">{{ $data['forecast']['historical_data_points'] ?? 0 }} data points</span>
            </div>

            <!-- Stock status -->
            <div class="mt-3 pt-3 border-t border-gray-100">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Current Stock:</span>
                    <span class="font-semibold {{ $data['item']->available_stock <= ($data['item']->low_stock_threshold ?? 5) ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $data['item']->available_stock }} / {{ $data['item']->total_stock }}
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12 text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <p class="text-lg">Not enough data for demand forecasting</p>
            <p class="text-sm mt-1">Forecasts require borrowing history to generate predictions</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
