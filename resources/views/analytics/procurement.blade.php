@extends('layouts.app')

@section('title', 'Procurement Analytics')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center space-x-4 animate-fade-in-up">
        <a href="{{ route('analytics.index') }}" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Procurement Analytics</h1>
            <p class="text-gray-600">Data-driven insights for optimized purchasing decisions</p>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-up stagger-1">
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 border-l-4 border-green-500">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Spending</p>
            <p class="text-3xl font-bold text-gray-900 mt-1 font-poppins">—</p>
            <p class="text-xs text-gray-500 mt-1">Hidden by client request</p>
        </div>
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 border-l-4 border-orange-500">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Pending Value</p>
            <p class="text-3xl font-bold text-orange-600 mt-1 font-poppins">—</p>
            <p class="text-xs text-gray-500 mt-1">Hidden by client request</p>
        </div>
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 border-l-4 border-blue-500">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Items Analyzed</p>
            <p class="text-3xl font-bold text-blue-600 mt-1 font-poppins">{{ count($procurementAnalytics) }}</p>
            <p class="text-xs text-gray-500 mt-1">With procurement history</p>
        </div>
    </div>

    <!-- Analytics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-fade-in-up stagger-2">
        @forelse($procurementAnalytics as $data)
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $data['item']->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $data['item']->category }}</p>
                </div>
                <span class="text-xs font-medium px-2.5 py-1 rounded-lg bg-green-100 text-green-700">
                    Stock: {{ $data['item']->quantity }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 uppercase">Avg Order Qty</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($data['analysis']['average_order_quantity'], 1) }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 uppercase">Avg Delivery Time</p>
                    <p class="text-lg font-bold text-gray-900">{{ $data['analysis']['average_delivery_time'] }} <span class="text-xs font-normal">days</span></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 uppercase">Reorder Point</p>
                    <p class="text-lg font-bold text-green-600">{{ $data['analysis']['recommended_reorder_point'] }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 uppercase">Optimal Order Qty</p>
                    <p class="text-lg font-bold text-green-600">{{ $data['analysis']['optimal_order_quantity'] }}</p>
                </div>
            </div>

            <div class="mt-4 bg-green-50 rounded-xl p-3 ring-1 ring-green-200">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-gray-500 uppercase">Monthly Demand Forecast</p>
                    <p class="text-lg font-bold text-green-700">{{ $data['analysis']['monthly_demand_forecast'] }} <span class="text-xs font-normal">units/mo</span></p>
                </div>
            </div>
        </div>
        @empty
        <div class="lg:col-span-2 bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-12 text-center text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <p class="text-lg">No procurement data available</p>
            <p class="text-sm mt-1">Procurement analytics will appear once orders are processed</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
