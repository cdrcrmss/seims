@extends('layouts.app')

@section('title', 'Overdue Risk Scoring')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Overdue Risk Scoring</h1>
            <p class="text-gray-600">Active borrowings ranked by return risk — higher score = more urgent</p>
        </div>
        <a href="{{ route('analytics.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">&larr; Back to Analytics</a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-red-50 rounded-xl ring-1 ring-red-200 p-4">
            <p class="text-xs font-bold text-red-600 uppercase">Critical</p>
            <p class="text-2xl font-bold text-red-700 mt-1">{{ $riskItems->where('risk_level', 'critical')->count() }}</p>
        </div>
        <div class="bg-orange-50 rounded-xl ring-1 ring-orange-200 p-4">
            <p class="text-xs font-bold text-orange-600 uppercase">High</p>
            <p class="text-2xl font-bold text-orange-700 mt-1">{{ $riskItems->where('risk_level', 'high')->count() }}</p>
        </div>
        <div class="bg-yellow-50 rounded-xl ring-1 ring-yellow-200 p-4">
            <p class="text-xs font-bold text-yellow-600 uppercase">Medium</p>
            <p class="text-2xl font-bold text-yellow-700 mt-1">{{ $riskItems->where('risk_level', 'medium')->count() }}</p>
        </div>
        <div class="bg-green-50 rounded-xl ring-1 ring-green-200 p-4">
            <p class="text-xs font-bold text-green-600 uppercase">Low</p>
            <p class="text-2xl font-bold text-green-700 mt-1">{{ $riskItems->where('risk_level', 'low')->count() }}</p>
        </div>
    </div>

    <!-- Risk Table -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Risk</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Item</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Borrower</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Due Date</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Past Late</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Score</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($riskItems as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold
                                {{ $item['risk_level'] === 'critical' ? 'bg-red-100 text-red-700' :
                                   ($item['risk_level'] === 'high' ? 'bg-orange-100 text-orange-700' :
                                   ($item['risk_level'] === 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700')) }}">
                                {{ strtoupper($item['risk_level']) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-sm font-semibold text-gray-900">{{ $item['item_name'] }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $item['borrower'] }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $item['expected_return'] }}</td>
                        <td class="px-6 py-3">
                            @if($item['is_overdue'])
                                <span class="text-red-600 font-bold text-sm">{{ abs($item['days_until_due']) }}d overdue</span>
                            @elseif($item['days_until_due'] <= 1)
                                <span class="text-orange-600 font-semibold text-sm">Due {{ $item['days_until_due'] === 0 ? 'today' : 'tomorrow' }}</span>
                            @else
                                <span class="text-gray-500 text-sm">{{ $item['days_until_due'] }}d remaining</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-sm {{ $item['user_past_overdue'] > 0 ? 'text-orange-600 font-bold' : 'text-gray-400' }}">{{ $item['user_past_overdue'] }}</td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $item['risk_score'] >= 70 ? 'bg-red-500' : ($item['risk_score'] >= 40 ? 'bg-orange-500' : ($item['risk_score'] >= 20 ? 'bg-yellow-500' : 'bg-green-500')) }}"
                                         style="width: {{ $item['risk_score'] }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-gray-600">{{ $item['risk_score'] }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">No active borrowings to score.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
