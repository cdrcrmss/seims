@extends('layouts.app')

@section('title', 'Item Management')

@section('content')
<div class="space-y-8" x-data="{ showAddItemModal: false, showImportModal: false, showQrModal: false, qrItem: null, showUnitsModal: false, unitsData: { item_id: null, item_name: '', units: [], total: 0 }, unitsLoading: false, async loadUnits(itemId) { this.unitsLoading = true; this.showUnitsModal = true; try { const res = await fetch('/staff/items/' + itemId + '/units'); this.unitsData = await res.json(); } catch(e) { this.unitsData = { item_id: itemId, item_name: 'Error', units: [], total: 0 }; } this.unitsLoading = false; this.$nextTick(() => { setTimeout(() => { this.unitsData.units.forEach(unit => { generateUnitQr(unit.id, unit.qr_code); }); }, 150); }); }, async disposeUnit(unit) { if (!confirm('Mark this unit as disposed? It cannot be borrowed.')) return; try { const res = await fetch('/staff/items/' + this.unitsData.item_id + '/units/' + unit.id, { method: 'PATCH', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, body: JSON.stringify({ status: 'disposed' }) }); const data = await res.json(); if (!res.ok) { alert(data.message || 'Could not dispose unit.'); return; } const idx = this.unitsData.units.findIndex(u => u.id === unit.id); if (idx !== -1) { this.unitsData.units[idx] = data.unit; } } catch (e) { alert('Could not dispose unit.'); } } }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Item Management</h1>
            <p class="text-gray-600">Manage laboratory equipment and inventory</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('staff.items.trash') }}" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Trash
            </a>
            <form method="POST" action="{{ route('qr.batch-generate') }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    Generate All QR
                </button>
            </form>
            <button @click="showImportModal = true" 
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Import Items
            </button>
            <button @click="showAddItemModal = true" 
                    class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Item
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <a href="{{ route('staff.items.index') }}" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover relative overflow-hidden cursor-pointer hover:ring-green-200 transition-all">
            <div class="absolute top-0 left-0 w-1 h-full bg-green-500 rounded-r-full"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Items</p>
                    <p class="text-3xl font-bold text-green-600 font-poppins">{{ $totalItemsCount }}</p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('staff.items.index', ['status' => 'available']) }}" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover relative overflow-hidden cursor-pointer hover:ring-emerald-200 transition-all">
            <div class="absolute top-0 left-0 w-1 h-full bg-emerald-500 rounded-r-full"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Available</p>
                    <p class="text-3xl font-bold text-emerald-600 font-poppins">{{ $availableItemsCount }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('staff.items.index', ['stock_filter' => 'out_of_stock']) }}" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover relative overflow-hidden cursor-pointer hover:ring-red-200 transition-all">
            <div class="absolute top-0 left-0 w-1 h-full bg-red-500 rounded-r-full"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Out of Stock</p>
                    <p class="text-3xl font-bold text-red-600 font-poppins">{{ $outOfStockCount }}</p>
                </div>
                <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('staff.items.index', ['status' => 'damaged']) }}" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover relative overflow-hidden cursor-pointer hover:ring-orange-200 transition-all">
            <div class="absolute top-0 left-0 w-1 h-full bg-orange-500 rounded-r-full"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Damaged Units</p>
                    <p class="text-3xl font-bold text-orange-600 font-poppins">{{ $damagedCount }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-xl font-bold text-gray-900">All Items</h2>
                <form method="GET" action="{{ route('staff.items.index') }}" class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="relative z-30" x-data="itemSearchComponent()" @click.outside="showSuggestions = false">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search items..."
                               x-model="query"
                               @input="onInput()"
                               @focus="onFocus()"
                               @keydown.escape="showSuggestions = false"
                               @keydown.arrow-down.prevent="highlightNext()"
                               @keydown.arrow-up.prevent="highlightPrev()"
                               @keydown.enter.prevent="selectHighlighted()"
                               autocomplete="off"
                               class="pl-10 pr-8 py-2 bg-gray-50 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-green-500 focus:border-green-500 w-64">
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <button x-show="query.length > 0" @click="clearSearch()" type="button" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        <!-- Suggestions Dropdown -->
                        <div x-show="showSuggestions && suggestions.length > 0"
                             x-cloak
                             @mousedown.prevent
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute left-0 top-full mt-2 w-72 bg-white rounded-xl shadow-lg border border-gray-200 z-[100] max-h-60 overflow-y-auto">
                            <template x-for="(suggestion, index) in suggestions" :key="suggestion.id">
                                <button type="button"
                                        @mousedown.prevent="selectSuggestion(suggestion)"
                                        :class="{ 'bg-green-50': highlightedIndex === index }"
                                        class="w-full text-left px-4 py-2.5 text-sm hover:bg-green-50 cursor-pointer transition-colors flex items-center gap-3 border-b border-gray-50 last:border-0">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate" x-text="suggestion.name"></p>
                                        <p class="text-xs text-gray-500 truncate" x-text="suggestion.detail"></p>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <select name="category" onchange="this.form.submit()"
                            class="px-4 py-2 bg-gray-50 border border-gray-300 rounded-xl text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>

                    <!-- Laboratory Filter -->
                    <select name="laboratory" onchange="this.form.submit()"
                            class="px-4 py-2 bg-gray-50 border border-gray-300 rounded-xl text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Laboratories</option>
                        @foreach($laboratories as $lab)
                            <option value="{{ $lab }}" {{ request('laboratory') == $lab ? 'selected' : '' }}>{{ $lab }}</option>
                        @endforeach
                    </select>

                    <!-- Stock Filter -->
                    <select name="stock_filter" onchange="this.form.submit()"
                            class="px-4 py-2 bg-gray-50 border border-gray-300 rounded-xl text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Stock</option>
                        <option value="in_stock" {{ request('stock_filter') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ request('stock_filter') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('stock_filter') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>

                    <!-- Item Status Filter -->
                    <select name="status" onchange="this.form.submit()"
                            class="px-4 py-2 bg-gray-50 border border-gray-300 rounded-xl text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Statuses</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="in_use" {{ request('status') == 'in_use' ? 'selected' : '' }}>In Use</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="damaged" {{ request('status') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                        <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                        <option value="disposed" {{ in_array(request('status'), ['disposed', 'retired']) ? 'selected' : '' }}>Disposed</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Added</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 transition-colors item-row" data-search="{{ strtolower($item->name . ' ' . ($item->description ?? '') . ' ' . $item->category . ' ' . $item->status) }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center overflow-hidden">
                                        @if($item->image_url)
                                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                        @if($item->description)
                                            <div class="text-sm text-gray-500">{{ Str::limit($item->description, 50) }}</div>
                                        @endif
                                        @if($item->location)
                                            <div class="text-xs text-gray-400 flex items-center gap-1 mt-0.5">
                                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                {{ $item->location }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $item->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->laboratory)
                                    @php
                                        $labColors = [
                                            'Alfresco' => 'bg-emerald-100 text-emerald-800',
                                            'Kitchen' => 'bg-amber-100 text-amber-800',
                                            'Food Lab' => 'bg-purple-100 text-purple-800',
                                            'Hotel' => 'bg-cyan-100 text-cyan-800',
                                        ];
                                        $labColor = $labColors[$item->laboratory] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $labColor }}">
                                        {{ $item->laboratory }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center space-x-2">
                                    <span class="font-medium">{{ $item->available_stock }}</span>
                                    <span class="text-gray-400">/</span>
                                    <span class="text-gray-500">{{ $item->total_stock }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="space-y-1">
                                    {{-- Item Status Badge --}}
                                    @switch($item->status)
                                        @case('available')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Available
                                            </span>
                                            @break
                                        @case('in_use')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                In Use
                                            </span>
                                            @break
                                        @case('maintenance')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                Maintenance
                                            </span>
                                            @break
                                        @case('damaged')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Damaged
                                            </span>
                                            @break
                                        @case('lost')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-800 text-white">
                                                Lost
                                            </span>
                                            @break
                                        @case('disposed')
                                        @case('retired')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-300 text-gray-700">
                                                Disposed
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ ucfirst($item->status ?? 'unknown') }}
                                            </span>
                                    @endswitch
                                    {{-- Stock Level Indicator --}}
                                    @if($item->available_stock <= 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-50 text-red-600">
                                            Out of Stock
                                        </span>
                                    @elseif($item->available_stock <= ($item->low_stock_threshold ?? 5))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-50 text-yellow-600">
                                            Low Stock
                                        </span>
                                    @endif
                                    {{-- Damaged Units Indicator --}}
                                    @if($item->damaged_units_count > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-50 text-orange-700">
                                            {{ $item->damaged_units_count }} damaged unit{{ $item->damaged_units_count > 1 ? 's' : '' }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-1">
                                    <button type="button" @click="loadUnits({{ $item->id }})" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200" title="View Units & QR Codes ({{ $item->total_stock }})">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                    </button>
                                    <a href="{{ route('staff.items.edit', $item) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200" title="Edit item">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('staff.items.delete', $item) }}" method="POST" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Delete Item', message: 'Are you sure you want to delete this item? This action cannot be undone.', type: 'danger' })">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50 transition-all duration-200" title="Delete item">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No items found</h3>
                                    <p class="text-gray-600">Get started by adding your first item.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($items->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $items->links() }}
        </div>
        @endif
    </div>
    <div x-show="showAddItemModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm" @click="showAddItemModal = false"></div>
            
            <!-- Modal panel -->
            <div class="relative z-10 w-full max-w-2xl bg-white rounded-3xl shadow-2xl transform transition-all"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="flex items-start justify-between p-6 border-b border-gray-200 rounded-t-3xl">
                    <h3 class="text-xl font-semibold text-gray-900">Add New Item</h3>
                    <button type="button" @click="showAddItemModal = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('staff.items.store') }}" enctype="multipart/form-data" class="p-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Item Name</label>
                            <input type="text" id="name" name="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                        </div>
                        <div>
                            <label for="quick_category" class="block mb-2 text-sm font-medium text-gray-900">Category</label>
                            @include('staff.items.partials.category-input', [
                                'categories' => $categories,
                                'inputId' => 'quick_category',
                                'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-green-500 focus:border-green-500 block w-full p-2.5',
                            ])
                        </div>
                        <div>
                            <label for="total_stock" class="block mb-2 text-sm font-medium text-gray-900">Total Stock</label>
                            <input type="number" id="total_stock" name="total_stock" min="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                        </div>
                        <div class="md:col-span-2">
                            <label for="description" class="block mb-2 text-sm font-medium text-gray-900">Description</label>
                            <textarea id="description" name="description" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-green-500 focus:border-green-500 block w-full p-2.5"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label for="image" class="block mb-2 text-sm font-medium text-gray-900">Item Image</label>
                            <input type="file" id="image" name="image" accept="image/*" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 mt-6">
                        <button type="submit" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Add Item
                        </button>
                        <button type="button" @click="showAddItemModal = false" class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- QR Code Modal -->
    <div x-show="showQrModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm" @click="showQrModal = false"></div>
            
            <div class="relative z-10 w-full max-w-md bg-white rounded-3xl shadow-2xl transform transition-all"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95">
                <div class="flex items-start justify-between p-6 border-b border-gray-200 rounded-t-3xl">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">QR Code</h3>
                        <p class="text-sm text-gray-500 mt-1" x-text="qrItem ? qrItem.name : ''"></p>
                    </div>
                    <button type="button" @click="showQrModal = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                    </button>
                </div>
                <div class="p-6">
                    <!-- QR Code needs generating -->
                    <div x-show="qrItem && !qrItem.qr_code" class="text-center py-6">
                        <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">This item doesn't have a QR code yet.</p>
                        <a :href="qrItem ? '/qr/generate/' + qrItem.id : '#'" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Generate QR Code
                        </a>
                    </div>

                    <!-- QR Code display -->
                    <div x-show="qrItem && qrItem.qr_code" class="text-center">
                        <div id="qr-code-container" class="inline-block bg-white p-4 rounded-2xl ring-1 ring-gray-100 mb-4">
                            <div id="item-qrcode" class="w-56 h-56 mx-auto"></div>
                        </div>

                        <div class="mb-4">
                            <div class="inline-flex items-center px-4 py-2 bg-gray-100 rounded-xl">
                                <span class="text-sm font-mono font-semibold text-gray-700" x-text="qrItem ? qrItem.qr_code : ''"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-left mb-5">
                            <div class="bg-gray-50 rounded-xl p-3">
                                <p class="text-xs text-gray-500 uppercase">Category</p>
                                <p class="text-sm font-semibold text-gray-900" x-text="qrItem ? qrItem.category : ''"></p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3">
                                <p class="text-xs text-gray-500 uppercase">Stock</p>
                                <p class="text-sm font-semibold text-gray-900" x-text="qrItem ? qrItem.available_stock + ' / ' + qrItem.total_stock : ''"></p>
                            </div>
                        </div>

                        <div class="flex gap-3 justify-center">
                            <button onclick="printQrCode()" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                Print
                            </button>
                            <button onclick="downloadQrCode()" class="inline-flex items-center gap-2 bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Download
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Units Modal -->
    <div x-show="showUnitsModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm" @click="showUnitsModal = false"></div>
            
            <div class="relative z-10 w-full max-w-3xl bg-white rounded-3xl shadow-2xl transform transition-all"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95">
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between p-6 border-b border-gray-200 rounded-t-3xl">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Individual Units</h3>
                        <p class="text-sm text-gray-500 mt-1" x-text="unitsData.item_name + ' — ' + unitsData.total + ' unit(s)'"></p>
                    </div>
                    <button type="button" @click="showUnitsModal = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <!-- Loading -->
                    <div x-show="unitsLoading" class="flex items-center justify-center py-12">
                        <div class="animate-spin w-8 h-8 border-4 border-green-600 border-t-transparent rounded-full"></div>
                    </div>

                    <!-- No units -->
                    <div x-show="!unitsLoading && unitsData.units.length === 0" class="text-center py-12">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <p class="text-sm text-gray-500">No individual units found for this item.</p>
                    </div>

                    <!-- Units Grid with QR Codes -->
                    <div x-show="!unitsLoading && unitsData.units.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="units-qr-grid">
                        <template x-for="(unit, idx) in unitsData.units" :key="unit.id">
                            <div class="bg-white rounded-xl ring-1 ring-gray-200 p-4 flex flex-col items-center text-center hover:shadow-md transition-shadow">
                                <!-- QR Code Image -->
                                <div class="w-28 h-28 mb-3 flex items-center justify-center" :id="'unit-qr-' + unit.id">
                                </div>
                                <!-- Unit Code -->
                                <p class="font-mono text-xs font-bold text-gray-900 mb-1" x-text="unit.unit_code"></p>
                                <!-- QR value (small) -->
                                <p class="font-mono text-[9px] text-gray-400 mb-2 break-all" x-text="unit.qr_code"></p>
                                <!-- Status & Condition -->
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold capitalize"
                                          :class="{
                                              'bg-green-100 text-green-700': unit.status === 'available',
                                              'bg-amber-100 text-amber-700': unit.status === 'borrowed',
                                              'bg-blue-100 text-blue-700': unit.status === 'maintenance',
                                              'bg-red-100 text-red-700': unit.status === 'lost' || unit.status === 'damaged',
                                              'bg-orange-100 text-orange-700': unit.status === 'needs_repair',
                                              'bg-gray-200 text-gray-600': unit.status === 'disposed' || unit.status === 'retired',
                                          }"
                                          x-text="unit.status === 'needs_repair' ? 'Needs Repair' : (unit.status === 'retired' ? 'disposed' : unit.status)"></span>
                                    <span class="text-[10px] font-semibold capitalize"
                                          :class="{
                                              'text-green-600': unit.condition === 'good',
                                              'text-yellow-600': unit.condition === 'fair',
                                              'text-orange-600': unit.condition === 'poor',
                                              'text-red-600': unit.condition === 'damaged',
                                          }"
                                          x-text="unit.condition"></span>
                                </div>
                                <!-- Current Holder -->
                                <p x-show="unit.current_borrower" class="text-[10px] font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full mt-1">
                                    <span x-text="'Held by: ' + unit.current_borrower"></span>
                                </p>
                                <button type="button"
                                        x-show="unit.status !== 'disposed' && unit.status !== 'retired' && unit.status !== 'borrowed'"
                                        @click="disposeUnit(unit)"
                                        class="mt-2 text-[10px] font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-2.5 py-1 rounded-lg transition-colors">
                                    Mark disposed
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 rounded-b-3xl bg-gray-50">
                    <p class="text-xs text-gray-500">Each unit has its own scannable QR code</p>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="printAllUnitQRCodes()" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-xl transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Print All QR
                        </button>
                        <button type="button" @click="showUnitsModal = false" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-100 rounded-xl ring-1 ring-gray-200 transition-all">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Items Modal -->
    <div x-show="showImportModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm" @click="showImportModal = false"></div>
            
            <div class="relative z-10 w-full max-w-lg bg-white rounded-3xl shadow-2xl transform transition-all"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="flex items-start justify-between p-6 border-b border-gray-200 rounded-t-3xl">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Import Items</h3>
                        <p class="text-sm text-gray-500 mt-1">Upload an Excel or CSV file to bulk add items</p>
                    </div>
                    <button type="button" @click="showImportModal = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('staff.items.bulk-import') }}" enctype="multipart/form-data" class="p-6">
                    @csrf
                    <div class="space-y-4">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition-colors">
                            <svg class="mx-auto w-10 h-10 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <label for="import_file" class="cursor-pointer">
                                <span class="text-sm font-medium text-blue-600 hover:text-blue-700">Click to upload</span>
                                <span class="text-sm text-gray-500"> or drag and drop</span>
                                <input type="file" id="import_file" name="import_file" accept=".xlsx,.xls,.csv" class="hidden" required>
                            </label>
                            <p class="text-xs text-gray-400 mt-2">Supports .xlsx, .xls, .csv (max 5MB)</p>
                        </div>
                        <div id="file-name-display" class="hidden text-sm text-gray-700 bg-gray-50 rounded-lg px-4 py-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span id="file-name-text"></span>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <p class="text-sm font-medium text-blue-800 mb-2">Required columns:</p>
                            <div class="grid grid-cols-2 gap-1 text-xs text-blue-700">
                                <span class="font-mono bg-blue-100 px-2 py-1 rounded">name</span>
                                <span class="font-mono bg-blue-100 px-2 py-1 rounded">category</span>
                                <span class="font-mono bg-blue-100 px-2 py-1 rounded">total_stock</span>
                                <span class="font-mono bg-blue-100 px-2 py-1 rounded">location</span>
                                <span class="font-mono bg-blue-100 px-2 py-1 rounded">laboratory</span>
                                <span class="text-blue-500 italic px-2 py-1">(required)</span>
                            </div>
                            <p class="text-sm font-medium text-blue-800 mt-3 mb-2">Optional columns:</p>
                            <div class="grid grid-cols-2 gap-1 text-xs text-blue-700">
                                <span class="font-mono bg-blue-100 px-2 py-1 rounded">description</span>
                                <span class="font-mono bg-blue-100 px-2 py-1 rounded">available_stock</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 mt-6">
                        <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Import Items
                        </button>
                        <button type="button" @click="showImportModal = false" class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    let currentQrInstance = null;
    let _qrItem = null;

    function generateQrCode(item) {
        _qrItem = item;
        const container = document.getElementById('item-qrcode');
        if (!container) return;

        // Clear previous QR
        container.innerHTML = '';
        currentQrInstance = null;

        if (!item || !item.qr_code) return;

        currentQrInstance = new QRCode(container, {
            text: item.qr_code,
            width: 224,
            height: 224,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    function printQrCode() {
        if (!_qrItem) return;

        const qrImg = document.querySelector('#item-qrcode img');
        const qrCanvas = document.querySelector('#item-qrcode canvas');
        const imgSrc = qrImg ? qrImg.src : (qrCanvas ? qrCanvas.toDataURL() : '');

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>QR Code - ${_qrItem.name}</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 40px; }
                    .qr-label { margin-top: 16px; }
                    .item-name { font-size: 18px; font-weight: bold; margin: 0; }
                    .item-category { font-size: 14px; color: #666; margin: 4px 0; }
                    .qr-code-id { font-size: 12px; font-family: monospace; color: #333; margin-top: 8px; background: #f3f4f6; padding: 4px 12px; border-radius: 6px; display: inline-block; }
                    img { width: 256px; height: 256px; }
                    @media print { body { padding: 20px; } }
                </style>
            </head>
            <body>
                <img src="${imgSrc}" alt="QR Code">
                <div class="qr-label">
                    <p class="item-name">${_qrItem.name}</p>
                    <p class="item-category">${_qrItem.category}</p>
                    <div class="qr-code-id">${_qrItem.qr_code}</div>
                </div>
                <script>window.onload = function() { window.print(); window.close(); }<\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }

    function downloadQrCode() {
        if (!_qrItem) return;

        const qrImg = document.querySelector('#item-qrcode img');
        const qrCanvas = document.querySelector('#item-qrcode canvas');
        const imgSrc = qrImg ? qrImg.src : (qrCanvas ? qrCanvas.toDataURL() : '');

        if (!imgSrc) return;

        const link = document.createElement('a');
        link.download = `QR-${_qrItem.qr_code}.png`;
        link.href = imgSrc;
        link.click();
    }

    function generateUnitQr(unitId, qrCode) {
        const container = document.getElementById('unit-qr-' + unitId);
        if (!container || !qrCode) return;
        container.innerHTML = '';
        new QRCode(container, {
            text: qrCode,
            width: 112,
            height: 112,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        });
    }

    function printAllUnitQRCodes() {
        const grid = document.getElementById('units-qr-grid');
        if (!grid) return;

        const cards = grid.querySelectorAll('[id^="unit-qr-"]');
        let qrItems = [];

        cards.forEach(card => {
            const img = card.querySelector('img');
            const canvas = card.querySelector('canvas');
            const imgSrc = img ? img.src : (canvas ? canvas.toDataURL() : '');
            const parent = card.closest('.bg-white');
            const unitCode = parent ? parent.querySelector('.font-mono.text-xs')?.textContent : '';
            if (imgSrc) {
                qrItems.push({ src: imgSrc, code: unitCode });
            }
        });

        if (qrItems.length === 0) return;

        const printWindow = window.open('', '_blank');
        const qrHtml = qrItems.map(item => `
            <div class="qr-item">
                <img src="${item.src}" alt="QR">
                <p class="code">${item.code}</p>
            </div>
        `).join('');

        printWindow.document.write(`
            <html>
            <head>
                <title>Unit QR Codes</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
                    .qr-item { text-align: center; padding: 16px; border: 1px solid #e5e7eb; border-radius: 12px; }
                    .qr-item img { width: 150px; height: 150px; margin: 0 auto 8px; }
                    .qr-item .code { font-family: monospace; font-size: 11px; font-weight: bold; margin: 0; }
                    @media print { body { padding: 10px; } .grid { gap: 16px; } .qr-item { break-inside: avoid; } }
                </style>
            </head>
            <body>
                <div class="grid">${qrHtml}</div>
                <script>window.onload = function() { window.print(); window.close(); }<\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }
</script>

<script>
    document.getElementById('import_file').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        const display = document.getElementById('file-name-display');
        const text = document.getElementById('file-name-text');
        if (fileName) {
            text.textContent = fileName;
            display.classList.remove('hidden');
            display.classList.add('flex');
        } else {
            display.classList.add('hidden');
            display.classList.remove('flex');
        }
    });
</script>

<script>
    function itemSearchComponent() {
        return {
            query: '{{ request("search") }}',
            suggestions: [],
            showSuggestions: false,
            highlightedIndex: -1,
            debounceTimer: null,

            onInput() {
                clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => {
                    if (this.query.trim().length >= 1) {
                        this.showSuggestions = true;
                        this.fetchSuggestions();
                    }
                    this.submitSearch();
                }, 400);
            },

            onFocus() {
                if (this.query.trim().length >= 1) {
                    this.fetchSuggestions();
                } else if (this.suggestions.length > 0) {
                    this.showSuggestions = true;
                }
            },

            submitSearch() {
                const form = this.$el.closest('form');
                if (!form) return;

                const params = new URLSearchParams(new FormData(form));
                params.delete('page');

                const qs = params.toString();
                window.location.href = form.action + (qs ? '?' + qs : '');
            },

            clearSearch() {
                this.query = '';
                this.suggestions = [];
                this.showSuggestions = false;
                this.submitSearch();
            },

            async fetchSuggestions() {
                const q = this.query.trim();
                if (q.length < 1) {
                    this.suggestions = [];
                    this.showSuggestions = false;
                    return;
                }
                try {
                    const res = await fetch(`/staff/items/search?q=${encodeURIComponent(q)}`, {
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                    });
                    const data = await res.json();
                    this.suggestions = (data.items || []).slice(0, 8).map(i => ({
                        id: i.id,
                        name: i.name,
                        detail: i.category,
                    }));
                    this.showSuggestions = this.showSuggestions && this.suggestions.length > 0;
                    this.highlightedIndex = -1;
                } catch (e) {
                    this.suggestions = [];
                    this.showSuggestions = false;
                }
            },

            selectSuggestion(suggestion) {
                this.query = suggestion.name;
                this.showSuggestions = false;
                this.highlightedIndex = -1;
                this.submitSearch();
            },

            highlightNext() {
                if (this.suggestions.length === 0) return;
                this.highlightedIndex = (this.highlightedIndex + 1) % this.suggestions.length;
            },

            highlightPrev() {
                if (this.suggestions.length === 0) return;
                this.highlightedIndex = this.highlightedIndex <= 0 ? this.suggestions.length - 1 : this.highlightedIndex - 1;
            },

            selectHighlighted() {
                if (this.highlightedIndex >= 0 && this.highlightedIndex < this.suggestions.length) {
                    this.selectSuggestion(this.suggestions[this.highlightedIndex]);
                } else {
                    this.submitSearch();
                }
            }
        };
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
