{{-- Item browser for borrow pages. Expects parent x-data="borrowForm()" --}}
@php
    $clearRoute = $filterRoute ?? url()->current();
@endphp
<div class="space-y-4">
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative" @click.outside="searchOpen = false">
                <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="searchQuery" @input="debouncedSearch()" @keydown.enter.prevent="applyFilters()"
                       @focus="onSearchFocus()"
                       placeholder="Search by name, description, or code..."
                       class="w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white"
                       :disabled="hasOverdue">
                <button type="button" x-show="searchQuery.length > 0" @click="clearSearch()" x-cloak
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <div x-show="searchOpen && searchResults.length > 0" x-cloak
                     class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-lg ring-1 ring-gray-200 z-50 max-h-64 overflow-y-auto">
                    <template x-for="item in searchResults" :key="item.id">
                        <div @click="pickSearchResult(item); searchOpen = false"
                             class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0 ring-1 ring-gray-100">
                                    <img x-show="item.image_url" :src="item.image_url" :alt="item.name" class="w-full h-full object-cover">
                                    <div x-show="!item.image_url" class="w-full h-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate" x-text="item.name"></p>
                                    <p class="text-xs text-gray-500" x-text="item.category"></p>
                                </div>
                                <span class="text-xs text-gray-400"><span x-text="item.available_stock"></span> avail.</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <select name="category" x-model="selectedCategory" @change="filterByCategory()"
                    class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white sm:w-44"
                    :disabled="hasOverdue">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ ($category ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="flex items-center justify-between px-1">
        <p class="text-xs text-gray-500">
            Showing <span class="font-semibold text-gray-700">{{ $availableItems->count() }}</span> of <span class="font-semibold text-gray-700">{{ $availableItems->total() }}</span> items
            @if($search) for "<span class="font-semibold text-gray-700">{{ $search }}</span>" @endif
            @if($category) in <span class="font-semibold text-gray-700">{{ $category }}</span> @endif
        </p>
        @if($search || $category)
            <a href="{{ $clearRoute }}" class="text-xs text-green-600 hover:text-green-700 font-semibold">Clear filters</a>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @forelse($availableItems as $item)
        <div @click="addToCart({ id: {{ $item->id }}, name: {{ json_encode($item->name) }}, category: {{ json_encode($item->category) }}, laboratory: {{ json_encode($item->laboratory ?? '') }}, stock: {{ $item->available_stock }}, image_url: {{ json_encode($item->image_url) }} })"
             :class="inCart({{ $item->id }}) ? 'ring-2 ring-green-500 bg-green-50/60' : 'ring-1 ring-gray-200 hover:ring-green-300 hover:shadow-md'"
             class="bg-white rounded-xl p-4 cursor-pointer transition-all duration-200 group relative"
             :class="hasOverdue ? 'opacity-50 pointer-events-none' : ''">

            <div x-show="inCart({{ $item->id }})" x-transition class="absolute top-3 right-3">
                <span class="text-[10px] font-bold uppercase tracking-wide text-green-700 bg-green-100 px-2 py-0.5 rounded-full">Added</span>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0 ring-1 ring-gray-100">
                    @if($item->image_path)
                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                    @else
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    @endif
                </div>
                <div class="flex-1 min-w-0 pr-14">
                    <h3 class="text-sm font-semibold text-gray-900 truncate group-hover:text-green-700 transition-colors">{{ $item->name }}</h3>
                    <p class="text-xs text-gray-500">{{ $item->category }}</p>
                    @if($item->laboratory)
                        <div class="mt-1">@include('partials.laboratory-badge', ['laboratory' => $item->laboratory])</div>
                    @endif
                    <div class="flex items-center gap-3 mt-2">
                        <span class="inline-flex items-center gap-1 text-xs font-medium {{ $item->available_stock > 5 ? 'text-green-600' : ($item->available_stock > 2 ? 'text-amber-600' : 'text-red-600') }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $item->available_stock > 5 ? 'bg-green-500' : ($item->available_stock > 2 ? 'bg-amber-500' : 'bg-red-500') }}"></span>
                            {{ $item->available_stock }} available
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="sm:col-span-2 bg-white rounded-xl ring-1 ring-gray-200 p-12 text-center">
            <p class="text-sm font-medium text-gray-500">No items found</p>
            @if($search || $category)
                <a href="{{ $clearRoute }}" class="text-xs text-green-600 hover:text-green-700 font-semibold mt-3">Clear filters</a>
            @endif
        </div>
        @endforelse
    </div>

    @if($availableItems->hasPages())
    <div class="mt-2">{{ $availableItems->links() }}</div>
    @endif
</div>
