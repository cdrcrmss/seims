@extends('layouts.app')

@section('title', 'Borrowing Requests')

@section('content')
<div class="space-y-8" x-data="{ statusFilter: '' }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">Borrowing Requests</h1>
            <p class="text-gray-600">Review and manage student borrowing requests</p>
        </div>
        <div class="flex items-center space-x-4">
            <!-- Status Filter with Alpine.js -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button" 
                        class="px-4 py-2 bg-white border border-gray-300 rounded-xl text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500 flex items-center justify-between min-w-[150px]">
                    <span x-text="statusFilter === '' ? 'All Status' : statusFilter.charAt(0).toUpperCase() + statusFilter.slice(1)">All Status</span>
                    <svg class="w-4 h-4 ml-2" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
                    <div class="py-1">
                        <button @click="statusFilter = ''; open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600">All Status</button>
                        <button @click="statusFilter = 'pending'; open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600">Pending</button>
                        <button @click="statusFilter = 'approved'; open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600">Approved</button>
                        <button @click="statusFilter = 'issued'; open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600">Issued</button>
                        <button @click="statusFilter = 'returned'; open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600">Returned</button>
                        <button @click="statusFilter = 'rejected'; open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600">Rejected</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <a href="{{ route('staff.borrowings.index', ['status' => 'pending']) }}" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover cursor-pointer hover:ring-amber-200 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Pending</p>
                    <p class="text-3xl font-bold text-amber-600 font-poppins">{{ $statusCounts['pending'] }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('staff.borrowings.index', ['status' => 'approved']) }}" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover cursor-pointer hover:ring-teal-200 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Approved</p>
                    <p class="text-3xl font-bold text-teal-600 font-poppins">{{ $statusCounts['approved'] }}</p>
                </div>
                <div class="w-12 h-12 bg-teal-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('staff.borrowings.index', ['status' => 'issued']) }}" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover cursor-pointer hover:ring-green-200 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Issued</p>
                    <p class="text-3xl font-bold text-green-600 font-poppins">{{ $statusCounts['issued'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('staff.borrowings.index', ['status' => 'returned']) }}" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover cursor-pointer hover:ring-emerald-200 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Returned</p>
                    <p class="text-3xl font-bold text-emerald-600 font-poppins">{{ $statusCounts['returned'] }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('staff.borrowings.index', ['status' => 'rejected']) }}" class="bg-white rounded-2xl p-6 ring-1 ring-gray-100 shadow-sm card-hover cursor-pointer hover:ring-red-200 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Rejected</p>
                    <p class="text-3xl font-bold text-red-600 font-poppins">{{ $statusCounts['rejected'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    <!-- Borrowing Requests -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-xl font-bold text-gray-900">All Requests</h2>
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="relative" x-data="searchComponent()" @click.away="showSuggestions = false">
                        <input type="text" id="searchInput" placeholder="Search requests..." 
                               x-model="query"
                               @input="filterRows(); updateSuggestions()"
                               @focus="onSearchFocus()"
                               @keydown.escape="showSuggestions = false"
                               @keydown.arrow-down.prevent="highlightNext()"
                               @keydown.arrow-up.prevent="highlightPrev()"
                               @keydown.enter.prevent="selectHighlighted()"
                               autocomplete="off"
                               class="pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-green-500 focus:border-green-500 w-64">
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <button x-show="query.length > 0" @click="query = ''; filterRows(); showSuggestions = false" type="button" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        <!-- Suggestions Dropdown -->
                        <div x-show="showSuggestions && suggestions.length > 0" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-lg border border-gray-200 z-50 max-h-60 overflow-y-auto">
                            <template x-for="(suggestion, index) in suggestions" :key="index">
                                <button @click="selectSuggestion(suggestion)" 
                                        :class="{ 'bg-green-50': highlightedIndex === index }"
                                        class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors flex items-center gap-3 border-b border-gray-50 last:border-0">
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
                </div>
            </div>
        </div>

        <div class="divide-y divide-white/10">
            @forelse($borrowings as $borrowing)
                <div class="p-6 hover:bg-white/5 transition-colors borrowing-row" data-search="{{ strtolower(($borrowing->item?->name ?? '') . ' ' . ($borrowing->user?->name ?? '') . ' ' . ($borrowing->user?->student_id ?? '') . ' ' . $borrowing->status . ' ' . ($borrowing->notes ?? '')) }}">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                        <!-- Request Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start space-x-4">
                                <!-- Item Image -->
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center overflow-hidden flex-shrink-0">
                                    @if($borrowing->item?->image_path)
                                        <img src="{{ $borrowing->item->image_url }}" alt="{{ $borrowing->item->name }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    @endif
                                </div>

                                <!-- Request Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $borrowing->item?->name ?? 'Deleted Item' }}</h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $borrowing->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($borrowing->status === 'approved' ? 'bg-blue-100 text-blue-800' : 
                                                ($borrowing->status === 'issued' ? 'bg-green-100 text-green-800' : 
                                                 ($borrowing->status === 'returned' ? 'bg-purple-100 text-purple-800' : 
                                                  'bg-red-100 text-red-800'))) }}">
                                            {{ ucfirst($borrowing->status) }}
                                        </span>
                                    </div>
                                    @if($borrowing->itemUnit)
                                        <p class="text-xs text-indigo-600 font-mono font-semibold mb-1">Unit: {{ $borrowing->itemUnit->unit_code }}</p>
                                    @endif

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500">Requested by</p>
                                            <p class="font-medium text-gray-900">{{ $borrowing->user?->name ?? 'Unknown User' }}</p>
                                            @if($borrowing->user?->student_id)
                                                <p class="text-xs text-gray-500">ID: {{ $borrowing->user->student_id }}</p>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Quantity</p>
                                            <p class="font-medium text-gray-900">{{ $borrowing->quantity }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Requested Date</p>
                                            <p class="font-medium text-gray-900">{{ $borrowing->created_at->format('M d, Y') }}</p>
                                        </div>
                                        @if($borrowing->expected_return_date)
                                            <div>
                                                <p class="text-gray-500">Expected Return</p>
                                                <p class="font-medium text-gray-900">{{ $borrowing->expected_return_date->format('M d, Y') }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    @if($borrowing->notes)
                                        <div class="mt-3">
                                            <p class="text-sm text-gray-600">
                                                <span class="font-medium">Notes:</span> {{ $borrowing->notes }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div style="width:176px;min-width:176px;" class="flex flex-col gap-2">
                            @if($borrowing->status === 'pending')
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('staff.borrowings.approve', $borrowing) }}" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full flex items-center justify-center gap-1.5 h-9 text-xs font-semibold rounded-lg bg-green-600 text-white hover:bg-green-700 shadow-sm transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('staff.borrowings.reject', $borrowing) }}" class="flex-1" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Reject Request', message: 'Are you sure you want to reject this borrow request?', type: 'danger' })">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full flex items-center justify-center gap-1.5 h-9 text-xs font-semibold rounded-lg text-red-700 bg-red-50 hover:bg-red-100 ring-1 ring-red-200 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            @elseif($borrowing->status === 'approved')
                                <button type="submit" form="issue-form-{{ $borrowing->id }}" class="w-full flex items-center justify-center gap-1.5 h-9 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Issue Item
                                </button>
                                <form id="issue-form-{{ $borrowing->id }}" method="POST" action="{{ route('staff.borrowings.issue', $borrowing) }}" class="hidden">
                                    @csrf
                                    @method('PATCH')
                                </form>
                            @elseif($borrowing->status === 'issued')
                                @php
                                    $isOverdue = $borrowing->expected_return_date && $borrowing->expected_return_date < now();
                                    $overdueDays = $isOverdue ? (int) now()->diffInDays($borrowing->expected_return_date) : 0;
                                    $expectedReturnFormatted = $borrowing->expected_return_date ? $borrowing->expected_return_date->format('M d, Y') : '';
                                @endphp
                                <button type="button" 
                                        onclick="openReturnModal({{ $borrowing->id }}, '{{ addslashes($borrowing->item?->name ?? 'Item') }}', '{{ addslashes($borrowing->user?->name ?? 'User') }}', '{{ $expectedReturnFormatted }}', {{ $isOverdue ? 'true' : 'false' }}, {{ $overdueDays }})" 
                                        class="w-full flex items-center justify-center gap-1.5 h-9 text-xs font-semibold rounded-lg {{ $isOverdue ? 'bg-red-600 hover:bg-red-700' : 'bg-purple-600 hover:bg-purple-700' }} text-white shadow-sm transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                    {{ $isOverdue ? 'Return (Overdue)' : 'Mark Returned' }}
                                </button>
                            @endif

                            @if($borrowing->status === 'returned' && $borrowing->return_condition)
                                <div class="w-full flex items-center justify-center gap-1.5 h-9 text-xs font-semibold rounded-lg
                                    {{ $borrowing->return_condition === 'good' ? 'bg-green-50 text-green-700 ring-1 ring-green-200' : 
                                       ($borrowing->return_condition === 'fair' ? 'bg-yellow-50 text-yellow-700 ring-1 ring-yellow-200' : 
                                        ($borrowing->return_condition === 'needs_repair' ? 'bg-orange-50 text-orange-700 ring-1 ring-orange-200' : 
                                         'bg-red-50 text-red-700 ring-1 ring-red-200')) }}">
                                    <span class="w-1.5 h-1.5 rounded-full 
                                        {{ $borrowing->return_condition === 'good' ? 'bg-green-500' : 
                                           ($borrowing->return_condition === 'fair' ? 'bg-yellow-500' : 
                                            ($borrowing->return_condition === 'needs_repair' ? 'bg-orange-500' : 'bg-red-500')) }}"></span>
                                    {{ ucfirst(str_replace('_', ' ', $borrowing->return_condition)) }}
                                </div>
                            @endif

                            <button onclick="showBorrowingDetails({{ $borrowing->toJson() }})" class="w-full flex items-center justify-center gap-1.5 h-9 text-xs font-semibold rounded-lg text-gray-600 bg-gray-50 hover:bg-gray-100 ring-1 ring-gray-200 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Details
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No borrowing requests</h3>
                        <p class="text-gray-600">No borrowing requests found matching your criteria.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($borrowings->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $borrowings->links() }}
            </div>
        @endif
    </div>

    <!-- Details Modal -->
    <div id="detailsModal" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeModal()"></div>
            
            <div class="relative z-10 w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Request Details</h3>
                    <button type="button" onclick="closeModal()" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div id="modalContent" class="px-6 py-5">
                    <!-- Content populated by JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function searchComponent() {
        return {
            query: '',
            suggestions: [],
            showSuggestions: false,
            highlightedIndex: -1,

            onSearchFocus() {
                if (this.query.trim().length > 0) {
                    this.updateSuggestions();
                } else if (this.suggestions.length > 0) {
                    this.showSuggestions = true;
                }
            },

            filterRows() {
                const q = this.query.toLowerCase().trim();
                document.querySelectorAll('.borrowing-row').forEach(row => {
                    const data = row.getAttribute('data-search') || '';
                    row.style.display = (q === '' || data.includes(q)) ? '' : 'none';
                });
            },

            updateSuggestions() {
                const q = this.query.toLowerCase().trim();
                if (q.length === 0) {
                    this.suggestions = [];
                    this.showSuggestions = false;
                    return;
                }

                const seen = new Set();
                const results = [];
                document.querySelectorAll('.borrowing-row').forEach(row => {
                    const data = row.getAttribute('data-search') || '';
                    if (data.includes(q)) {
                        const nameEl = row.querySelector('h3');
                        const userEl = row.querySelector('.text-gray-900.font-medium');
                        const name = nameEl ? nameEl.textContent.trim() : '';
                        const user = userEl ? userEl.textContent.trim() : '';
                        const key = name + '|' + user;
                        if (!seen.has(key)) {
                            seen.add(key);
                            results.push({ name: name, detail: 'Requested by ' + user });
                        }
                    }
                });
                this.suggestions = results.slice(0, 6);
                this.showSuggestions = results.length > 0;
                this.highlightedIndex = -1;
            },

            selectSuggestion(suggestion) {
                this.query = suggestion.name;
                this.showSuggestions = false;
                this.filterRows();
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
                }
            }
        };
    }

    function closeModal() {
        document.getElementById('detailsModal').classList.add('hidden');
    }

    function showBorrowingDetails(borrowing) {
        const statusMap = {
            pending: { bg: 'bg-amber-50', text: 'text-amber-700', dot: 'bg-amber-500' },
            approved: { bg: 'bg-blue-50', text: 'text-blue-700', dot: 'bg-blue-500' },
            issued: { bg: 'bg-green-50', text: 'text-green-700', dot: 'bg-green-500' },
            returned: { bg: 'bg-purple-50', text: 'text-purple-700', dot: 'bg-purple-500' },
            rejected: { bg: 'bg-red-50', text: 'text-red-700', dot: 'bg-red-500' },
        };
        const s = statusMap[borrowing.status] || statusMap.pending;
        
        const itemName = borrowing.item ? borrowing.item.name : 'Deleted Item';
        const itemCategory = borrowing.item ? borrowing.item.category : '—';
        const unitCode = borrowing.item_unit ? borrowing.item_unit.unit_code : null;
        const unitQr = borrowing.item_unit ? borrowing.item_unit.qr_code : null;
        const userName = borrowing.user ? borrowing.user.name : 'Unknown User';
        const userEmail = borrowing.user ? borrowing.user.email : '—';
        const studentId = borrowing.user && borrowing.user.student_id ? borrowing.user.student_id : null;

        const content = `
            <div class="space-y-5">
                <!-- Item & Status -->
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-base font-bold text-gray-900">${itemName}</h4>
                        <p class="text-xs text-gray-500">${itemCategory}</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-md ${s.bg} ${s.text}">
                        <span class="w-1.5 h-1.5 rounded-full ${s.dot}"></span>
                        ${borrowing.status.charAt(0).toUpperCase() + borrowing.status.slice(1)}
                    </span>
                </div>

                ${unitCode ? `
                <div class="bg-gray-50 rounded-lg px-3 py-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Assigned Unit</p>
                        <p class="text-sm font-bold text-gray-900 font-mono">${unitCode}</p>
                        ${unitQr ? `<p class="text-[10px] text-gray-400 font-mono truncate">${unitQr}</p>` : ''}
                    </div>
                </div>` : ''}

                <hr class="border-gray-100">

                <!-- Student Info -->
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Student Information</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-gray-500">Name</p>
                            <p class="text-sm font-semibold text-gray-900">${userName}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="text-sm font-semibold text-gray-900 truncate">${userEmail}</p>
                        </div>
                        ${studentId ? `
                        <div>
                            <p class="text-xs text-gray-500">Student ID</p>
                            <p class="text-sm font-semibold text-gray-900">${studentId}</p>
                        </div>` : ''}
                    </div>
                </div>

                <hr class="border-gray-100">

                <!-- Borrowing Info -->
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Borrowing Details</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-gray-500">Quantity</p>
                            <p class="text-sm font-semibold text-gray-900">${borrowing.quantity}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Request Date</p>
                            <p class="text-sm font-semibold text-gray-900">${new Date(borrowing.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</p>
                        </div>
                        ${borrowing.expected_return_date ? `
                        <div>
                            <p class="text-xs text-gray-500">Expected Return</p>
                            <p class="text-sm font-semibold text-gray-900">${new Date(borrowing.expected_return_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</p>
                        </div>` : ''}
                        ${borrowing.approved_date ? `
                        <div>
                            <p class="text-xs text-gray-500">Approved Date</p>
                            <p class="text-sm font-semibold text-gray-900">${new Date(borrowing.approved_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</p>
                        </div>` : ''}
                        ${borrowing.issued_date ? `
                        <div>
                            <p class="text-xs text-gray-500">Issued Date</p>
                            <p class="text-sm font-semibold text-gray-900">${new Date(borrowing.issued_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</p>
                        </div>` : ''}
                        ${borrowing.returned_date ? `
                        <div>
                            <p class="text-xs text-gray-500">Returned Date</p>
                            <p class="text-sm font-semibold text-gray-900">${new Date(borrowing.returned_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</p>
                        </div>` : ''}
                        ${borrowing.return_condition ? `
                        <div>
                            <p class="text-xs text-gray-500">Return Condition</p>
                            <p class="text-sm font-semibold text-gray-900">${borrowing.return_condition.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>
                        </div>` : ''}
                    </div>
                </div>

                ${(borrowing.approver || borrowing.issuer || borrowing.rejector || borrowing.returned_to_user) ? `
                <hr class="border-gray-100">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Action History</p>
                    <div class="space-y-2">
                        ${borrowing.approver ? `
                        <div class="flex items-center gap-2 bg-blue-50 rounded-lg px-3 py-2">
                            <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-blue-700"><span class="font-semibold">Approved by</span> ${borrowing.approver.name}</p>
                                ${borrowing.approved_date ? `<p class="text-[10px] text-blue-500">${new Date(borrowing.approved_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</p>` : ''}
                            </div>
                        </div>` : ''}
                        ${borrowing.issuer ? `
                        <div class="flex items-center gap-2 bg-green-50 rounded-lg px-3 py-2">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-green-700"><span class="font-semibold">Issued by</span> ${borrowing.issuer.name}</p>
                                ${borrowing.issued_date ? `<p class="text-[10px] text-green-500">${new Date(borrowing.issued_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</p>` : ''}
                            </div>
                        </div>` : ''}
                        ${borrowing.rejector ? `
                        <div class="flex items-center gap-2 bg-red-50 rounded-lg px-3 py-2">
                            <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-red-700"><span class="font-semibold">Rejected by</span> ${borrowing.rejector.name}</p>
                                ${borrowing.rejected_date ? `<p class="text-[10px] text-red-500">${new Date(borrowing.rejected_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</p>` : ''}
                            </div>
                        </div>` : ''}
                        ${borrowing.returned_to_user ? `
                        <div class="flex items-center gap-2 bg-purple-50 rounded-lg px-3 py-2">
                            <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-purple-700"><span class="font-semibold">Returned to</span> ${borrowing.returned_to_user.name}</p>
                                ${borrowing.returned_date ? `<p class="text-[10px] text-purple-500">${new Date(borrowing.returned_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</p>` : ''}
                            </div>
                        </div>` : ''}
                    </div>
                </div>` : ''}

                ${borrowing.notes ? `
                <hr class="border-gray-100">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Notes</p>
                    <p class="text-sm text-gray-700 leading-relaxed">${borrowing.notes}</p>
                </div>` : ''}

                ${borrowing.rejection_reason ? `
                <div class="bg-red-50 rounded-lg p-3 mt-2">
                    <p class="text-xs font-semibold text-red-700 mb-0.5">Rejection Reason</p>
                    <p class="text-sm text-red-600">${borrowing.rejection_reason}</p>
                </div>` : ''}

                ${borrowing.return_notes ? `
                <div class="bg-gray-50 rounded-lg p-3 mt-2">
                    <p class="text-xs font-semibold text-gray-600 mb-0.5">Return Notes</p>
                    <p class="text-sm text-gray-700">${borrowing.return_notes}</p>
                </div>` : ''}
            </div>
        `;
        
        document.getElementById('modalContent').innerHTML = content;
        document.getElementById('detailsModal').classList.remove('hidden');
    }

    // Return Modal Functions
    function openReturnModal(borrowingId, itemName, userName, expectedReturn, isOverdue, overdueDays) {
        document.getElementById('returnBorrowingId').value = borrowingId;
        document.getElementById('returnForm').action = `/staff/borrowings/${borrowingId}/return`;
        document.getElementById('returnItemName').textContent = itemName || 'Item';
        document.getElementById('returnUserName').textContent = userName || 'User';
        
        // Show overdue warning if applicable
        const overdueEl = document.getElementById('returnOverdueWarning');
        const overdueMsg = document.getElementById('returnOverdueMsg');
        if (isOverdue && overdueDays > 0) {
            overdueEl.classList.remove('hidden');
            overdueMsg.textContent = `This item is ${overdueDays} day${overdueDays > 1 ? 's' : ''} overdue (expected: ${expectedReturn})`;
        } else {
            overdueEl.classList.add('hidden');
        }
        
        document.getElementById('returnModal').classList.remove('hidden');
        document.querySelectorAll('#returnForm input[name="return_condition"]').forEach(r => r.checked = false);
        document.getElementById('return_notes').value = '';
        clearReturnImage();
    }

    function closeReturnModal() {
        document.getElementById('returnModal').classList.add('hidden');
        clearReturnImage();
        document.getElementById('return_notes').value = '';
        document.querySelectorAll('#returnForm input[name="return_condition"]').forEach(r => r.checked = false);
    }

    function previewReturnImage(input) {
        const file = input.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imageFileName').textContent = file.name;
            document.getElementById('imagePreview').classList.remove('hidden');
            document.getElementById('imagePlaceholder').classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }

    function clearReturnImage() {
        document.getElementById('return_image').value = '';
        document.getElementById('previewImg').src = '';
        document.getElementById('imageFileName').textContent = '';
        document.getElementById('imagePreview').classList.add('hidden');
        document.getElementById('imagePlaceholder').classList.remove('hidden');
    }
</script>

<!-- Return Modal -->
<div id="returnModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeReturnModal()"></div>
        
        <div class="relative z-10 w-full max-w-sm bg-white rounded-2xl shadow-xl overflow-hidden">
            <form id="returnForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <input type="hidden" id="returnBorrowingId" name="borrowing_id" value="">
                
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Return Item</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        <span id="returnItemName" class="font-medium text-gray-700"></span>
                        &middot; by <span id="returnUserName" class="font-medium text-gray-700"></span>
                    </p>
                </div>
                
                <div class="px-6 py-5 space-y-4">
                    <!-- Overdue Warning -->
                    <div id="returnOverdueWarning" class="hidden bg-red-50 ring-1 ring-red-200 rounded-xl p-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p id="returnOverdueMsg" class="text-xs font-semibold text-red-700"></p>
                    </div>

                    <!-- Condition -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Item Condition</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="return_condition" value="good" class="peer hidden" required>
                                <div class="peer-checked:ring-2 peer-checked:ring-green-500 peer-checked:bg-green-50 border border-gray-200 rounded-xl p-3 text-center transition-all hover:border-green-300">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-xs font-semibold text-gray-700">Good</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="return_condition" value="fair" class="peer hidden">
                                <div class="peer-checked:ring-2 peer-checked:ring-yellow-500 peer-checked:bg-yellow-50 border border-gray-200 rounded-xl p-3 text-center transition-all hover:border-yellow-300">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-xs font-semibold text-gray-700">Fair</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="return_condition" value="needs_repair" class="peer hidden">
                                <div class="peer-checked:ring-2 peer-checked:ring-orange-500 peer-checked:bg-orange-50 border border-gray-200 rounded-xl p-3 text-center transition-all hover:border-orange-300">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="text-xs font-semibold text-gray-700">Needs Repair</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="return_condition" value="damaged" class="peer hidden">
                                <div class="peer-checked:ring-2 peer-checked:ring-red-500 peer-checked:bg-red-50 border border-gray-200 rounded-xl p-3 text-center transition-all hover:border-red-300">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    <span class="text-xs font-semibold text-gray-700">Damaged</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Notes -->
                    <div>
                        <label for="return_notes" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Notes <span class="font-normal normal-case text-gray-400">(optional)</span></label>
                        <textarea id="return_notes" name="return_notes" rows="2" 
                                  class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none"
                                  placeholder="Any observations about the item..."></textarea>
                    </div>

                    <!-- Return Image -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                            Return Photo <span class="font-normal normal-case text-gray-400">(optional)</span>
                        </label>
                        <div id="imageUploadArea"
                             class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center cursor-pointer hover:border-green-400 transition-colors"
                             onclick="document.getElementById('return_image').click()">
                            <div id="imagePreview" class="hidden">
                                <img id="previewImg" src="" alt="Return photo" class="max-h-28 mx-auto rounded-lg object-cover">
                                <p id="imageFileName" class="text-xs text-gray-500 mt-2 truncate"></p>
                                <button type="button" onclick="event.stopPropagation(); clearReturnImage()"
                                        class="mt-1 text-xs text-red-500 hover:underline">Remove</button>
                            </div>
                            <div id="imagePlaceholder">
                                <svg class="w-8 h-8 mx-auto text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p class="text-xs text-gray-500">Click to upload a photo</p>
                                <p class="text-xs text-gray-400">JPG, PNG, WebP – max 5 MB</p>
                            </div>
                        </div>
                        <input type="file" id="return_image" name="return_image" accept="image/*" class="hidden"
                               onchange="previewReturnImage(this)">
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="flex gap-2 px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <button type="button" onclick="closeReturnModal()" 
                            class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 inline-flex items-center justify-center gap-1.5 bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Confirm Return
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection