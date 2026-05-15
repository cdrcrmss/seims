@extends('layouts.app')

@section('title', 'Borrow an Item')

@section('content')
<div class="space-y-6" x-data="borrowForm()">

    <!-- Header -->
    <div class="flex items-center gap-4 animate-fade-in-up">
        <a href="{{ route('student.borrowings.index') }}" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-poppins">Borrow an Item</h1>
            <p class="text-sm text-gray-500">Browse available items and submit a borrowing request</p>
        </div>
    </div>

    <!-- Alerts -->
    @if($hasOverdue)
    <div class="flex items-center gap-3 bg-red-50 rounded-xl p-4 ring-1 ring-red-200 animate-fade-in-up">
        <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-red-800">You have overdue items!</p>
            <p class="text-xs text-red-600">Please return your overdue items before making new borrowing requests.</p>
        </div>
    </div>
    @endif

    @if($activeBorrowCount >= $maxItems)
    <div class="flex items-center gap-3 bg-amber-50 rounded-xl p-4 ring-1 ring-amber-200 animate-fade-in-up">
        <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-amber-800">Borrowing limit reached ({{ $activeBorrowCount }}/{{ $maxItems }})</p>
            <p class="text-xs text-amber-600">Please wait for existing requests to be completed or cancel existing ones.</p>
        </div>
    </div>
    @endif

    @if($errors->has('rate_limit'))
    <div class="bg-red-50 ring-1 ring-red-200 text-red-700 rounded-xl p-4 text-sm flex items-center gap-3 animate-fade-in-up">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ $errors->first('rate_limit') }}
    </div>
    @endif

    @if($errors->any() && !$errors->has('rate_limit'))
    <div class="bg-red-50 rounded-xl p-4 ring-1 ring-red-200 animate-fade-in-up">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="text-sm font-semibold text-red-800 mb-1">Please fix the following:</p>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-0.5">
                    @foreach($errors->all() as $error)
                        @if($error !== $errors->first('rate_limit'))
                            <li>{{ $error }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 ring-1 ring-red-200 text-red-700 rounded-xl p-4 text-sm flex items-center gap-3 animate-fade-in-up">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <!-- Left: Item Browsing (3 cols) -->
        <div class="lg:col-span-3 space-y-4 animate-fade-in-up stagger-1">
            <!-- Search & Filter -->
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-4">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 relative" x-data="{ searchOpen: false }">
                        <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" x-model="searchQuery" @input="debouncedSearch()" @focus="searchOpen = true" @click.away="searchOpen = false"
                               placeholder="Search by name, description, or code..."
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white">
                        
                        <!-- Search Results Dropdown -->
                        <div x-show="searchOpen && searchResults.length > 0" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-lg ring-1 ring-gray-200 z-50 max-h-64 overflow-y-auto">
                            <template x-for="item in searchResults" :key="item.id">
                                <div @click="selectItem(item.id, item.name, item.category, item.available_stock); searchOpen = false; searchQuery = ''"
                                     class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-0">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate" x-text="item.name"></p>
                                            <p class="text-xs text-gray-500" x-text="item.category"></p>
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            <span x-text="item.available_stock"></span> available
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <select name="category" x-model="selectedCategory" @change="filterByCategory()"
                            class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white sm:w-44">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ ($category ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Result Count -->
            <div class="flex items-center justify-between px-1">
                <p class="text-xs text-gray-500">
                    Showing <span class="font-semibold text-gray-700">{{ $availableItems->count() }}</span> of <span class="font-semibold text-gray-700">{{ $availableItems->total() }}</span> items
                    @if($search) for "<span class="font-semibold text-gray-700">{{ $search }}</span>" @endif
                    @if($category) in <span class="font-semibold text-gray-700">{{ $category }}</span> @endif
                </p>
                @if($search || $category)
                    <a href="{{ route('student.borrow.form') }}" class="text-xs text-green-600 hover:text-green-700 font-semibold">Clear filters</a>
                @endif
            </div>

            <!-- Item Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @forelse($availableItems as $item)
                <div @click="selectItem({{ $item->id }}, {{ json_encode($item->name) }}, {{ json_encode($item->category) }}, {{ $item->available_stock }})"
                     :class="selectedItemId == {{ $item->id }} ? 'ring-2 ring-green-500 bg-green-50/60' : 'ring-1 ring-gray-200 hover:ring-green-300 hover:shadow-md'"
                     class="bg-white rounded-xl p-4 cursor-pointer transition-all duration-200 group relative">

                    {{-- Selected badge --}}
                    <div x-show="selectedItemId == {{ $item->id }}" x-transition class="absolute top-3 right-3">
                        <span class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </span>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0">
                            @if($item->image_path)
                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0 pr-6">
                            <h3 class="text-sm font-semibold text-gray-900 truncate group-hover:text-green-700 transition-colors">{{ $item->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $item->category }}</p>
                            @if($item->description)
                                <p class="text-xs text-gray-400 mt-1 line-clamp-1">{{ Str::limit($item->description, 60) }}</p>
                            @endif
                            <div class="flex items-center gap-3 mt-2">
                                <span class="inline-flex items-center gap-1 text-xs font-medium {{ $item->available_stock > 5 ? 'text-green-600' : ($item->available_stock > 2 ? 'text-amber-600' : 'text-red-600') }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $item->available_stock > 5 ? 'bg-green-500' : ($item->available_stock > 2 ? 'bg-amber-500' : 'bg-red-500') }}"></span>
                                    {{ $item->available_stock }} available
                                </span>
                                @if($item->asset_code)
                                    <span class="text-[10px] text-gray-400 font-mono">{{ $item->asset_code }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="sm:col-span-2 bg-white rounded-xl ring-1 ring-gray-200 p-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <p class="text-sm font-medium text-gray-500">No items found</p>
                    <p class="text-xs text-gray-400 mt-1">Try adjusting your search or filters</p>
                    @if($search || $category)
                        <a href="{{ route('student.borrow.form') }}" class="inline-flex items-center gap-1 text-xs text-green-600 hover:text-green-700 font-semibold mt-3">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Clear filters
                        </a>
                    @endif
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($availableItems->hasPages())
            <div class="mt-2">
                {{ $availableItems->links() }}
            </div>
            @endif
        </div>

        <!-- Right: Form + Sidebar (2 cols) -->
        <div class="lg:col-span-2 space-y-4 animate-fade-in-up stagger-2">
            <!-- Borrow Form Card -->
            <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm sticky top-6 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-base font-bold text-gray-900 font-poppins">Borrowing Request</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Select an item, then fill in the details</p>
                </div>

                <form method="POST" action="{{ route('student.borrow') }}" @submit="handleSubmit($event)" class="p-6 space-y-5">
                    @csrf
                    <input type="hidden" name="item_id" :value="selectedItemId">

                    <!-- Selected Item -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Selected Item</label>

                        <div x-show="!selectedItemId" class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <p class="text-sm text-gray-400 font-medium">No item selected</p>
                            <p class="text-xs text-gray-300 mt-0.5">Click an item from the list</p>
                        </div>

                        <div x-show="selectedItemId" x-cloak class="bg-green-50 ring-1 ring-green-200 rounded-xl p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-green-900" x-text="selectedItemName"></p>
                                        <p class="text-xs text-green-700" x-text="selectedItemCategory"></p>
                                    </div>
                                </div>
                                <button type="button" @click="clearSelection()" class="p-1.5 text-green-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="mt-2 pt-2 border-t border-green-200 flex items-center justify-between">
                                <span class="text-xs text-green-600">Stock available</span>
                                <span class="text-sm font-bold text-green-700" x-text="selectedItemStock"></span>
                            </div>
                        </div>
                        @error('item_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Quantity & Return Date -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="quantity" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Quantity</label>
                            <input type="number" id="quantity" name="quantity"
                                   x-model="quantity"
                                   :max="Math.min(selectedItemStock || 10, 10)"
                                   min="1" value="{{ old('quantity', 1) }}"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   :disabled="!selectedItemId">
                            <p class="text-[11px] text-gray-400 mt-1">Max: <span x-text="Math.min(selectedItemStock || 10, 10)"></span></p>
                            @error('quantity') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="expected_return_date" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Return Date</label>
                            <input type="date" id="expected_return_date" name="expected_return_date"
                                   value="{{ old('expected_return_date') }}"
                                   min="{{ now()->addDay()->toDateString() }}"
                                   max="{{ now()->addDays($maxDays)->toDateString() }}"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   :disabled="!selectedItemId">
                            <p class="text-[11px] text-gray-400 mt-1">Within {{ $maxDays }} days</p>
                            @error('expected_return_date') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Purpose -->
                    <div>
                        <label for="purpose" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                            Purpose <span class="font-normal normal-case text-gray-400">(min 10 characters)</span>
                        </label>
                        <textarea id="purpose" name="purpose" rows="3"
                                  x-model="purpose" maxlength="500"
                                  placeholder="Describe why you need to borrow this item..."
                                  class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none"
                                  :disabled="!selectedItemId">{{ old('purpose') }}</textarea>
                        <div class="flex justify-between mt-1">
                            @error('purpose') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                            <span class="text-xs ml-auto" :class="purpose.length >= 10 ? 'text-green-600' : 'text-gray-400'" x-text="purpose.length + '/500'"></span>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                            Additional Notes <span class="font-normal normal-case text-gray-400">(optional)</span>
                        </label>
                        <textarea id="notes" name="notes" rows="2"
                                  maxlength="500"
                                  placeholder="Any additional information..."
                                  class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none"
                                  :disabled="!selectedItemId">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Info Bar -->
                    <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Active Requests</span>
                            <div class="flex items-center gap-2">
                                <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full {{ $activeBorrowCount >= $maxItems ? 'bg-red-500' : 'bg-green-500' }}" style="width: {{ min(($activeBorrowCount / max(1, $maxItems)) * 100, 100) }}%"></div>
                                </div>
                                <span class="text-xs font-bold {{ $activeBorrowCount >= $maxItems ? 'text-red-600' : 'text-gray-900' }}">{{ $activeBorrowCount }}/{{ $maxItems }}</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Borrow Period</span>
                            <span class="text-xs font-bold text-gray-900">{{ $maxDays }} days max</span>
                        </div>
                        <div class="flex justify-between items-center pt-1.5 border-t border-gray-200">
                            <span class="text-xs text-gray-400 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Rate limit
                            </span>
                            <span class="text-xs text-gray-400">3 requests / 5 min</span>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex gap-3 pt-1">
                        <button type="submit"
                                :disabled="isSubmitting"
                                :class="isSubmitting ? 'bg-gray-300 cursor-not-allowed' : (!canSubmit ? 'bg-green-500 hover:bg-green-600' : 'bg-green-600 hover:bg-green-700 shadow-sm hover:shadow-md')"
                                class="flex-1 py-3 text-white font-semibold rounded-xl text-sm transition-all duration-200 flex items-center justify-center gap-2">
                            <template x-if="isSubmitting">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            </template>
                            <template x-if="!isSubmitting">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </template>
                            <span x-text="isSubmitting ? 'Submitting...' : 'Submit Request'"></span>
                        </button>
                        <a href="{{ route('student.borrowings.index') }}" class="inline-flex items-center justify-center px-5 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition-colors">
                            Cancel
                        </a>
                    </div>

                    <p class="text-[10px] text-gray-400 text-center leading-relaxed">
                        By submitting, you agree to return the item in good condition by the expected return date.
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function borrowForm() {
    return {
        selectedItemId: {{ old('item_id', $selectedItem?->id ?? 'null') }},
        selectedItemName: {!! json_encode(old('item_id') ? (\App\Models\Item::find(old('item_id'))?->name ?? '') : ($selectedItem?->name ?? '')) !!},
        selectedItemCategory: {!! json_encode(old('item_id') ? (\App\Models\Item::find(old('item_id'))?->category ?? '') : ($selectedItem?->category ?? '')) !!},
        selectedItemStock: {{ old('item_id') ? (\App\Models\Item::find(old('item_id'))?->available_stock ?? 0) : ($selectedItem?->available_stock ?? 0) }},
        quantity: {{ old('quantity', 1) }},
        purpose: {!! json_encode(old('purpose', '')) !!},
        isSubmitting: false,
        searchQuery: '',
        searchResults: [],
        searchTimeout: null,
        selectedCategory: {!! json_encode($category ?? '') !!},

        filterByCategory() {
            const params = new URLSearchParams(window.location.search);
            if (this.selectedCategory) {
                params.set('category', this.selectedCategory);
            } else {
                params.delete('category');
            }
            params.delete('page');
            window.location.href = '{{ route('student.borrow.form') }}' + (params.toString() ? '?' + params.toString() : '');
        },

        get canSubmit() {
            return this.selectedItemId &&
                   this.quantity > 0 &&
                   this.purpose.length >= 10 &&
                   !{{ $hasOverdue ? 'true' : 'false' }} &&
                   {{ $activeBorrowCount }} < {{ $maxItems }};
        },

        debouncedSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.performSearch();
            }, 150);
        },

        async performSearch() {
            if (this.searchQuery.length < 1) {
                this.searchResults = [];
                return;
            }

            try {
                const response = await fetch('{{ route('student.api.search-items') }}?q=' + encodeURIComponent(this.searchQuery));
                const data = await response.json();
                this.searchResults = data;
            } catch (error) {
                console.error('Search failed:', error);
                this.searchResults = [];
            }
        },

        selectItem(id, name, category, stock) {
            if ({{ $hasOverdue ? 'true' : 'false' }} || {{ $activeBorrowCount }} >= {{ $maxItems }}) return;

            this.selectedItemId = id;
            this.selectedItemName = name;
            this.selectedItemCategory = category;
            this.selectedItemStock = stock;

            if (this.quantity > Math.min(stock, 10)) {
                this.quantity = Math.min(stock, 10);
            }
        },

        clearSelection() {
            this.selectedItemId = null;
            this.selectedItemName = '';
            this.selectedItemCategory = '';
            this.selectedItemStock = 0;
            this.quantity = 1;
        },

        handleSubmit(event) {
            if (this.isSubmitting) {
                event.preventDefault();
                return;
            }
            this.isSubmitting = true;
        }
    }
}
</script>
@endsection
