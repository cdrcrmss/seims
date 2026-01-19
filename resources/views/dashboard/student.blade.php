@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<!-- Welcome Section -->
<div class="mb-8 animate-fade-in">
    <div class="relative bg-gradient-to-r from-green-700 via-green-600 to-green-600 rounded-3xl p-8 shadow-2xl overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full -ml-16 -mb-16"></div>
        <div class="absolute top-1/2 right-20 w-24 h-24 bg-white/5 rounded-full"></div>
        <div class="relative flex items-center justify-between">
            <div>
                <p class="text-white/80 text-sm font-medium">Welcome back,</p>
                <h1 class="text-3xl font-bold text-white mb-2 font-poppins">
                    {{ auth()->user()->name }} 👋
                </h1>
                <p class="text-white/70">
                    Browse available laboratory equipment and manage your borrowing requests.
                </p>
            </div>
            <div class="hidden lg:block">
                <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="group glass rounded-2xl p-6 shadow-xl card-hover animate-slide-up" style="animation-delay: 0.1s;">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Active Requests</p>
                <p class="text-4xl font-bold text-green-600 dark:text-green-400 font-poppins">{{ $activeBorrowings->count() }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Currently borrowed</p>
            </div>
            <div class="w-16 h-16 bg-gradient-to-br from-green-500/20 to-green-600/30 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="group glass rounded-2xl p-6 shadow-xl card-hover animate-slide-up" style="animation-delay: 0.2s;">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Available Items</p>
                <p class="text-4xl font-bold text-green-600 dark:text-green-400 font-poppins">{{ $availableItems->count() }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ready to borrow</p>
            </div>
            <div class="w-16 h-16 bg-gradient-to-br from-green-500/20 to-green-600/30 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="group glass rounded-2xl p-6 shadow-xl card-hover animate-slide-up" style="animation-delay: 0.3s;">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Total Borrowed</p>
                <p class="text-4xl font-bold text-emerald-600 dark:text-emerald-400 font-poppins">{{ $borrowingHistory->count() }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Lifetime total</p>
            </div>
            <div class="w-16 h-16 bg-gradient-to-br from-emerald-500/20 to-emerald-600/30 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Available Items Section -->
<div class="mb-8 animate-fade-in" style="animation-delay: 0.4s;">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white font-poppins">Available Items</h2>
            <p class="text-gray-600 dark:text-gray-400">Browse and borrow laboratory equipment</p>
        </div>
        <div x-data="{ filter: 'all' }" class="flex flex-wrap gap-2">
            <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-green-600 text-white shadow-lg scale-105' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 border-gray-200 dark:border-gray-700'" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 border">
                All Items
            </button>
            @foreach($itemsByCategory as $category => $items)
                <button @click="filter = '{{ strtolower($category) }}'" :class="filter === '{{ strtolower($category) }}' ? 'bg-green-600 text-white shadow-lg scale-105' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 border-gray-200 dark:border-gray-700'" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 border">
                    {{ $category }} ({{ $items->count() }})
                </button>
            @endforeach
        </div>
    </div>

        <!-- Items Grid -->
    <div x-data="{ filter: 'all', showBorrowModal: false, selectedItem: null }">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($availableItems as $item)
            <div x-show="filter === 'all' || filter === '{{ strtolower($item->category) }}'"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="group relative glass rounded-3xl p-6 shadow-xl card-hover overflow-hidden">
                
                <!-- Gradient overlay -->
                <div class="absolute inset-0 bg-gradient-to-br from-transparent via-transparent to-green-500/5 dark:to-green-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-3xl"></div>
                
                <!-- Stock status indicator -->
                <div class="absolute top-4 right-4 z-10">
                    <div class="flex items-center space-x-1">
                        <div class="w-3 h-3 rounded-full {{ $item->available_stock > 5 ? 'bg-green-400 animate-pulse' : ($item->available_stock > 0 ? 'bg-yellow-400 animate-pulse' : 'bg-red-400') }}"></div>
                        <span class="text-xs font-semibold {{ $item->available_stock > 5 ? 'text-green-600 dark:text-green-400' : ($item->available_stock > 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                            {{ $item->available_stock }}
                        </span>
                    </div>
                </div>
                
                <!-- Item Image -->
                <div class="relative mb-4">
                    <div class="w-full h-48 bg-gradient-to-br from-gray-100 via-white to-gray-100 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700 rounded-2xl flex items-center justify-center overflow-hidden group-hover:scale-105 transition-transform duration-300">
                        @if($item->image_path)
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="relative">
                                <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 group-hover:text-green-500 dark:group-hover:text-green-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-500/20 rounded-full animate-ping"></div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Item Info -->
                <div class="relative z-10 space-y-3">
                    <!-- Category badge -->
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 text-green-700 dark:text-green-300">
                            {{ $item->category }}
                        </span>
                    </div>
                    
                    <!-- Item name -->
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors font-poppins line-clamp-2">
                        {{ $item->name }}
                    </h3>
                    
                    <!-- Description -->
                    @if($item->description)
                        <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2 leading-relaxed">
                            {{ $item->description }}
                        </p>
                    @endif

                    <!-- Stock info -->
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">
                            Total Stock: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $item->total_stock }}</span>
                        </span>
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Action button -->
                    <div class="pt-3">
                        @if($item->available_stock > 0)
                            <button @click="selectedItem = {{ $item->toJson() }}; showBorrowModal = true" 
                                    class="w-full btn-primary group/btn flex items-center justify-center space-x-2 py-3 rounded-xl font-semibold transition-all duration-300 hover:shadow-xl">
                                <svg class="w-5 h-5 group-hover/btn:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Borrow Item</span>
                            </button>
                        @else
                            <button disabled class="w-full py-3 px-4 bg-gray-300/50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 font-semibold rounded-xl cursor-not-allowed flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636"></path>
                                </svg>
                                <span>Out of Stock</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
        
        <!-- Empty state when no items match filter -->
        <div x-show="filter !== 'all' && document.querySelectorAll('[x-show*="filter"]').length === 0" class="col-span-full flex flex-col items-center justify-center py-16 text-center">
            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No items found</h3>
            <p class="text-gray-600 dark:text-gray-400">Try selecting a different category or check back later.</p>
        </div>
        </div>
            <!-- Borrow Modal -->
            <div x-show="showBorrowModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showBorrowModal" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0" 
                         x-transition:enter-end="opacity-100"
                         @click="showBorrowModal = false"
                         class="fixed inset-0 transition-opacity bg-gray-500/50 backdrop-blur-sm"></div>

                    <div x-show="showBorrowModal" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         class="inline-block align-bottom bg-white dark:bg-gray-800 backdrop-blur-md rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-gray-700">
                        
                        <form method="POST" action="{{ route('student.borrow') }}" class="p-6">
                            @csrf
                            <input type="hidden" name="item_id" :value="selectedItem?.id">
                            
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Request to Borrow</h3>
                                <p class="text-gray-600 dark:text-gray-300" x-text="selectedItem?.name"></p>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity</label>
                                    <input type="number" name="quantity" id="quantity" min="1" :max="selectedItem?.available_stock" value="1" required
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>

                                <div>
                                    <label for="expected_return_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Expected Return Date</label>
                                    <input type="date" name="expected_return_date" id="expected_return_date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>

                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes (Optional)</label>
                                    <textarea name="notes" id="notes" rows="3" maxlength="500" 
                                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                              placeholder="Any special requirements or notes..."></textarea>
                                </div>
                            </div>

                            <div class="flex space-x-3 mt-6">
                                <button type="button" @click="showBorrowModal = false"
                                        class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="flex-1 px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-colors shadow-lg">
                                    Submit Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </div>
</div>

    <!-- Active Borrowings -->
    @if($activeBorrowings->count() > 0)
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Your Active Requests</h2>
            <div class="space-y-4">
                @foreach($activeBorrowings as $borrowing)
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $borrowing->item->name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Quantity: {{ $borrowing->quantity }}</p>
                                    @if($borrowing->expected_return_date)
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Expected return: {{ $borrowing->expected_return_date->format('M d, Y') }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    {{ $borrowing->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/20 dark:text-yellow-300' : 
                                       ($borrowing->status === 'approved' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800/20 dark:text-blue-300' : 
                                        'bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-300') }}">
                                    {{ ucfirst($borrowing->status) }}
                                </span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $borrowing->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<style>
    [x-cloak] { display: none !important; }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
