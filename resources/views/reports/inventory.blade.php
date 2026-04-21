@extends('layouts.app')

@section('title', 'Inventory Report')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Inventory Report</h1>
            <p class="text-gray-600">Stock levels, wear analysis, and asset valuation</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">&larr; Reports</a>
            <form method="POST" action="{{ route('reports.export') }}">
                @csrf
                <input type="hidden" name="type" value="inventory">
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all">Export CSV</button>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Total Items</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $items->count() }}</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Total Value</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">₱{{ number_format($totalValue, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Critical Wear</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $wearBuckets['critical'] }}</p>
        </div>
        <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase">Damaged/Retired</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ ($statusCounts['damaged'] ?? 0) + ($statusCounts['retired'] ?? 0) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Status Breakdown -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Status Distribution</h2>
            @php
                $statusColors = ['available' => 'bg-green-500', 'in_use' => 'bg-blue-500', 'maintenance' => 'bg-orange-500', 'damaged' => 'bg-red-500', 'retired' => 'bg-gray-500', 'lost' => 'bg-purple-500'];
                $totalItems = max(1, array_sum($statusCounts));
            @endphp
            <div class="flex rounded-full h-4 overflow-hidden mb-4">
                @foreach($statusCounts as $status => $count)
                <div class="{{ $statusColors[$status] ?? 'bg-gray-400' }}" style="width: {{ ($count / $totalItems) * 100 }}%" title="{{ ucfirst($status) }}: {{ $count }}"></div>
                @endforeach
            </div>
            <div class="grid grid-cols-2 gap-2">
                @foreach($statusCounts as $status => $count)
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full {{ $statusColors[$status] ?? 'bg-gray-400' }}"></div>
                    <span class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                    <span class="text-sm font-bold text-gray-900 ml-auto">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Wear Distribution -->
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Wear Level Distribution</h2>
            @php
                $wearColors = ['good' => 'bg-green-500', 'moderate' => 'bg-yellow-500', 'worn' => 'bg-orange-500', 'critical' => 'bg-red-500'];
                $wearLabels = ['good' => '0-29%', 'moderate' => '30-59%', 'worn' => '60-79%', 'critical' => '80-100%'];
                $totalWear = max(1, array_sum($wearBuckets));
            @endphp
            <div class="flex rounded-full h-4 overflow-hidden mb-4">
                @foreach($wearBuckets as $level => $count)
                <div class="{{ $wearColors[$level] }}" style="width: {{ ($count / $totalWear) * 100 }}%"></div>
                @endforeach
            </div>
            <div class="grid grid-cols-2 gap-2">
                @foreach($wearBuckets as $level => $count)
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full {{ $wearColors[$level] }}"></div>
                    <span class="text-sm text-gray-600">{{ ucfirst($level) }} ({{ $wearLabels[$level] }})</span>
                    <span class="text-sm font-bold text-gray-900 ml-auto">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Items Table with Filters -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap gap-3 items-center">
            <form method="GET" class="flex flex-wrap gap-2 items-center">
                <select name="category" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                <select name="status" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach(['available', 'in_use', 'maintenance', 'damaged', 'retired', 'lost'] as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Name</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Category</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Stock</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase">Wear</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-semibold text-gray-900">
                            <a href="{{ route('timeline.asset', $item) }}" class="hover:text-blue-600">{{ $item->name }}</a>
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $item->category }}</td>
                        <td class="px-6 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold {{ $item->status === 'available' ? 'bg-green-100 text-green-700' : ($item->status === 'damaged' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $item->available_stock }}/{{ $item->total_stock }}</td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $item->wear_level >= 80 ? 'bg-red-500' : ($item->wear_level >= 60 ? 'bg-orange-500' : ($item->wear_level >= 30 ? 'bg-yellow-500' : 'bg-green-500')) }}"
                                         style="width: {{ $item->wear_level }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-gray-600">{{ $item->wear_level }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
