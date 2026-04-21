@extends('layouts.app')

@section('title', 'Asset Timeline - ' . $item->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">{{ $item->name }}</h1>
            <p class="text-gray-600">Complete asset lifecycle timeline</p>
        </div>
        <a href="{{ route('staff.items.edit', $item) }}" class="inline-flex items-center gap-2 bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 rounded-xl font-semibold transition-all">
            Edit Item
        </a>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Total Borrows</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $kpis['total_borrows'] }}</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Repairs</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $kpis['repair_count'] }}</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">MTTR (hrs)</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $kpis['mttr_hours'] ?? 'N/A' }}</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Failures (90d)</p>
            <p class="text-2xl font-bold {{ $kpis['repeat_failures_90d'] > 2 ? 'text-red-600' : 'text-gray-900' }} mt-1">{{ $kpis['repeat_failures_90d'] }}</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Wear Level</p>
            <div class="mt-2 flex items-center gap-2">
                <div class="flex-1 bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $kpis['current_wear'] >= 80 ? 'bg-red-500' : ($kpis['current_wear'] >= 50 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $kpis['current_wear'] }}%"></div>
                </div>
                <span class="text-sm font-bold">{{ $kpis['current_wear'] }}%</span>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Event Timeline</h2>
        <div class="relative">
            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
            <div class="space-y-6">
                @forelse($events->take(50) as $event)
                <div class="relative pl-10">
                    <div class="absolute left-2.5 w-3 h-3 rounded-full ring-2 ring-white
                        @switch($event['color'])
                            @case('green') bg-green-500 @break
                            @case('blue') bg-blue-500 @break
                            @case('yellow') bg-yellow-500 @break
                            @case('orange') bg-orange-500 @break
                            @case('red') bg-red-500 @break
                            @default bg-gray-400
                        @endswitch
                    "></div>
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $event['title'] }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $event['detail'] }}</p>
                        </div>
                        <time class="text-xs text-gray-400 whitespace-nowrap ml-4">{{ $event['timestamp']->format('M d, g:ia') }}</time>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-400 py-12">No events recorded for this item yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
