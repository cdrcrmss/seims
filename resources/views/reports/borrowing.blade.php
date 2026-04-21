@extends('layouts.app')

@section('title', 'Borrowing Report')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Borrowing Report</h1>
            <p class="text-gray-600">Activity summary for the last {{ $days }} days</p>
        </div>
        <div class="flex gap-2 items-center">
            <form method="GET" class="flex gap-2 items-center">
                <select name="days" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5" onchange="this.form.submit()">
                    @foreach([7, 30, 60, 90] as $d)
                    <option value="{{ $d }}" {{ $days == $d ? 'selected' : '' }}>{{ $d }} days</option>
                    @endforeach
                </select>
            </form>
            <form method="POST" action="{{ route('reports.export') }}">
                @csrf
                <input type="hidden" name="type" value="borrowing">
                <input type="hidden" name="days" value="{{ $days }}">
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all">Export CSV</button>
            </form>
            <a href="{{ route('reports.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">&larr; Reports</a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Total</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalBorrowings }}</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Returned</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $returnedCount }}</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Active</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $activeCount }}</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Overdue Now</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $overdueCount }}</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Late Rate</p>
            <p class="text-2xl font-bold {{ $overdueRate > 20 ? 'text-red-600' : ($overdueRate > 10 ? 'text-orange-600' : 'text-green-600') }} mt-1">{{ $overdueRate }}%</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Avg Turnaround</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $avgTurnaround ?? 'N/A' }}<span class="text-sm font-normal text-gray-400">d</span></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Borrowers -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Top Borrowers</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($topBorrowers as $i => $tb)
                <div class="px-6 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-gray-400 w-5">{{ $i + 1 }}</span>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $tb->user?->name ?? 'Deleted User' }}</p>
                            <p class="text-xs text-gray-500">{{ $tb->user?->student_id }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-blue-600">{{ $tb->borrow_count }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Top Items -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Most Borrowed Items</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($topItems as $i => $ti)
                <div class="px-6 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-gray-400 w-5">{{ $i + 1 }}</span>
                        <p class="text-sm font-semibold text-gray-900">{{ $ti->item?->name ?? 'Deleted Item' }}</p>
                    </div>
                    <span class="text-sm font-bold text-green-600">{{ $ti->borrow_count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Return Condition Breakdown -->
    @if(!empty($conditionBreakdown))
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Return Condition Breakdown</h2>
        @php
            $condColors = ['good' => 'bg-green-500', 'fair' => 'bg-yellow-500', 'needs_repair' => 'bg-orange-500', 'damaged' => 'bg-red-500'];
            $condLabels = ['good' => 'Good', 'fair' => 'Fair', 'needs_repair' => 'Needs Repair', 'damaged' => 'Damaged'];
            $totalCond = max(1, array_sum($conditionBreakdown));
        @endphp
        <div class="flex rounded-full h-4 overflow-hidden mb-4">
            @foreach($conditionBreakdown as $cond => $count)
            <div class="{{ $condColors[$cond] ?? 'bg-gray-400' }}" style="width: {{ ($count / $totalCond) * 100 }}%"></div>
            @endforeach
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach($conditionBreakdown as $cond => $count)
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full {{ $condColors[$cond] ?? 'bg-gray-400' }}"></div>
                <span class="text-sm text-gray-600">{{ $condLabels[$cond] ?? ucfirst($cond) }}</span>
                <span class="text-sm font-bold text-gray-900 ml-auto">{{ $count }} ({{ round(($count / $totalCond) * 100) }}%)</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
