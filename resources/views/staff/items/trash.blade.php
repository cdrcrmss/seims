@extends('layouts.app')

@section('title', 'Trash - Item Management')

@section('content')
<div class="space-y-8" x-data="trashSearch()" x-init="initSearch()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Trash</h1>
            <p class="text-gray-600">View and manage deleted items</p>
        </div>
        <div>
            <a href="{{ route('staff.items.index') }}" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Items
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 relative">
        <form method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}" 
                       placeholder="Search by name, asset code, or QR code..." 
                       x-model="searchQuery"
                       @input="debouncedSearch"
                       @focus="onSearchFocus()"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       autocomplete="off">
                
                <!-- Search Results Dropdown -->
                <div x-show="showDropdown && searchResults.length > 0" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translateY(-10)"
                     x-transition:enter-end="opacity-100 translateY(0"
                     class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-xl ring-1 ring-gray-200 z-50 max-h-64 overflow-y-auto">
                    <template x-for="item in searchResults" :key="item.id">
                        <a :href="'?search=' + item.name" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 truncate" x-text="item.name"></p>
                                    <p class="text-xs text-gray-500 truncate" x-text="item.asset_code || 'N/A'"></p>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
            <div class="sm:w-48">
                <select name="category" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
                Search
            </button>
            <a href="{{ route('staff.items.trash') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 text-center">
                Clear
            </a>
        </form>
    </div>

    <!-- Trashed Items Table -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Item</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Category</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Asset Code</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Stock</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Deleted At</th>
                        <th class="text-right px-6 py-4 font-semibold text-gray-600 uppercase tracking-wide text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($trashedItems as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                @if($item->image_url)
                                    <img src="{{ $item->image_url }}" class="w-10 h-10 rounded-lg object-cover" alt="">
                                @else
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900">{{ $item->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->qr_code ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $item->category }}</td>
                        <td class="px-6 py-4 text-gray-700 font-mono text-xs">{{ $item->asset_code ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <span class="text-gray-700">{{ $item->available_stock }} / {{ $item->total_stock }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs">{{ $item->deleted_at->format('M d, Y - g:i A') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <form method="POST" action="{{ route('staff.items.restore', $item->id) }}" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Restore Item', message: 'Are you sure you want to restore this item?', type: 'success' })">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-50 text-green-700 hover:bg-green-100 ring-1 ring-green-200/60 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Restore
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('staff.items.force-delete', $item->id) }}" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Permanently Delete', message: 'Are you sure you want to permanently delete this item? This action cannot be undone.', type: 'danger' })">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-50 text-red-700 hover:bg-red-100 ring-1 ring-red-200/60 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Delete Permanently
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            <p>No items in trash</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($trashedItems->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $trashedItems->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function trashSearch() {
    return {
        searchQuery: '',
        searchResults: [],
        showDropdown: false,
        debounceTimer: null,

        initSearch() {
            this.searchQuery = '{{ $search ?? '' }}';
        },

        onSearchFocus() {
            if (this.searchQuery.trim().length >= 1) {
                this.performSearch();
            } else if (this.searchResults.length > 0) {
                this.showDropdown = true;
            }
        },

        debouncedSearch() {
            clearTimeout(this.debounceTimer);
            
            // Trigger on first letter
            if (this.searchQuery.length >= 1) {
                this.debounceTimer = setTimeout(() => {
                    this.performSearch();
                }, 200);
            } else {
                this.showDropdown = false;
                this.searchResults = [];
            }
        },

        async performSearch() {
            if (this.searchQuery.length < 1) {
                this.showDropdown = false;
                this.searchResults = [];
                return;
            }

            try {
                const response = await fetch('/staff/api/trashed-items?search=' + encodeURIComponent(this.searchQuery), {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                this.searchResults = data.items || [];
                this.showDropdown = this.searchResults.length > 0;
            } catch (error) {
                console.error('Search error:', error);
                this.searchResults = [];
                this.showDropdown = false;
            }
        },

        closeDropdown() {
            this.showDropdown = false;
        }
    };
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.relative')) {
        if (window.trashSearchInstance) {
            window.trashSearchInstance.showDropdown = false;
        }
    }
});
</script>

@endsection
