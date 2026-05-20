@extends('layouts.app')

@section('title', 'Borrowing Management')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">{{ $archived === '1' ? 'Archived Borrowings' : 'Borrowing Management' }}</h1>
            <p class="text-gray-600">
                @if(!empty($overdue))
                    Showing overdue borrowings that need attention
                @elseif(!empty($active))
                    Showing active borrowings (approved and issued)
                @elseif($archived === '1')
                    View archived borrowing records
                @else
                    View and manage all borrowing transactions
                @endif
            </p>
        </div>
        <div>
            @if($archived === '1')
                <a href="{{ route('admin.borrowings') }}" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                    Back to Active
                </a>
            @else
                <a href="{{ route('admin.borrowings', ['archived' => '1']) }}" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    View Archives
                </a>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 animate-fade-in-up stagger-1 relative z-10">
        <form method="GET" action="{{ route('admin.borrowings') }}" class="flex flex-col md:flex-row gap-4">
            @if($archived === '1')<input type="hidden" name="archived" value="1">@endif
            @if(!empty($active))<input type="hidden" name="active" value="1">@endif
            @if(!empty($overdue))<input type="hidden" name="overdue" value="1">@endif
            <div class="flex-1 relative" x-data="borrowingSearchComponent()" @click.away="showSuggestions = false">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by user or item name..."
                        x-model="query"
                        @input="filterRows(); updateSuggestions()"
                        @focus="onSearchFocus()"
                        @keydown.escape="showSuggestions = false"
                        @keydown.arrow-down.prevent="highlightNext()"
                        @keydown.arrow-up.prevent="highlightPrev()"
                        @keydown.enter.prevent="selectHighlighted()"
                        autocomplete="off"
                        class="w-full pl-10 pr-8 py-2.5 rounded-xl ring-1 ring-gray-200 focus:ring-2 focus:ring-green-500 focus:outline-none text-sm">
                    <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <button x-show="query.length > 0" @click="query = ''; filterRows(); showSuggestions = false" type="button" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Suggestions Dropdown -->
                <div x-show="showSuggestions && suggestions.length > 0" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute left-0 mt-2 w-full max-w-sm bg-white rounded-xl shadow-lg border border-gray-200 z-50 max-h-60 overflow-y-auto">
                    <template x-for="(suggestion, index) in suggestions" :key="index">
                        <button type="button" @click="selectSuggestion(suggestion)" 
                                :class="{ 'bg-green-50': highlightedIndex === index }"
                                class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors flex items-center gap-3 border-b border-gray-50 last:border-0">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="suggestion.name"></p>
                                <p class="text-xs text-gray-500 truncate" x-text="suggestion.detail"></p>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
            <div>
                <select name="status" class="w-full md:w-48 px-4 py-2.5 rounded-xl ring-1 ring-gray-200 focus:ring-2 focus:ring-green-500 focus:outline-none text-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="issued" {{ $status === 'issued' ? 'selected' : '' }}>Issued</option>
                    <option value="returned" {{ $status === 'returned' ? 'selected' : '' }}>Returned</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filter
            </button>
            @if($search || $status || !empty($active) || !empty($overdue))
            <a href="{{ route('admin.borrowings', $archived === '1' ? ['archived' => '1'] : []) }}" class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 animate-fade-in-up stagger-1">
        @php
            $allBorrowings = \App\Models\Borrowing::all();
            $stats = [
                ['label' => 'Pending', 'count' => $allBorrowings->where('status', 'pending')->count(), 'color' => 'yellow', 'status' => 'pending'],
                ['label' => 'Approved', 'count' => $allBorrowings->where('status', 'approved')->count(), 'color' => 'blue', 'status' => 'approved'],
                ['label' => 'Issued', 'count' => $allBorrowings->where('status', 'issued')->count(), 'color' => 'green', 'status' => 'issued'],
                ['label' => 'Returned', 'count' => $allBorrowings->where('status', 'returned')->count(), 'color' => 'gray', 'status' => 'returned'],
                ['label' => 'Overdue', 'count' => $allBorrowings->where('status', 'issued')->where('expected_return_date', '<', now())->count(), 'color' => 'red', 'status' => 'issued', 'overdue' => true],
            ];
        @endphp
        @foreach($stats as $stat)
        <a href="{{ route('admin.borrowings', !empty($stat['overdue']) ? ['overdue' => 1] : ['status' => $stat['status']]) }}" class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-4 text-center border-l-4 border-{{ $stat['color'] }}-500 hover:ring-{{ $stat['color'] }}-300 transition-all cursor-pointer block">
            <p class="text-2xl font-bold text-gray-900 font-poppins">{{ $stat['count'] }}</p>
            <p class="text-xs text-gray-500 uppercase font-semibold">{{ $stat['label'] }}</p>
        </a>
        @endforeach
    </div>

    <!-- Borrowings Table -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden animate-fade-in-up stagger-2">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">User</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Item</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Qty</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Requested</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Return Date</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($borrowings as $borrowing)
                    @php
                        $isOverdue = $borrowing->status === 'issued' && $borrowing->expected_return_date && $borrowing->expected_return_date < now();
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'approved' => 'bg-blue-100 text-blue-700',
                            'issued' => $isOverdue ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700',
                            'returned' => 'bg-gray-100 text-gray-600',
                            'cancelled' => 'bg-gray-100 text-gray-500',
                            'rejected' => 'bg-red-100 text-red-600',
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors borrowing-row" data-search="{{ strtolower(($borrowing->user?->name ?? '') . ' ' . ($borrowing->item?->name ?? '') . ' ' . $borrowing->status . ' ' . ($borrowing->user?->role ?? '')) }}">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $borrowing->user?->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst($borrowing->user?->role ?? '') }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 text-sm">{{ $borrowing->item?->name ?? 'N/A' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $borrowing->quantity }}</td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg {{ $statusColors[$borrowing->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $isOverdue ? 'Overdue' : ucfirst($borrowing->status) }}
                            </span>
                            @if($borrowing->approver && in_array($borrowing->status, ['approved', 'issued', 'returned']))
                                <p class="text-[10px] text-gray-400 mt-1">Approved by {{ $borrowing->approver->name }}</p>
                            @endif
                            @if($borrowing->issuer && in_array($borrowing->status, ['issued', 'returned']))
                                <p class="text-[10px] text-gray-400">Issued by {{ $borrowing->issuer->name }}</p>
                            @endif
                            @if($borrowing->rejector && $borrowing->status === 'rejected')
                                <p class="text-[10px] text-gray-400 mt-1">Rejected by {{ $borrowing->rejector->name }}</p>
                            @endif
                            @if($borrowing->returnedToUser && $borrowing->status === 'returned')
                                <p class="text-[10px] text-gray-400">Returned to {{ $borrowing->returnedToUser->name }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $borrowing->requested_date ? \Carbon\Carbon::parse($borrowing->requested_date)->format('M d, Y') : 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $borrowing->expected_return_date ? \Carbon\Carbon::parse($borrowing->expected_return_date)->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1.5">
                                @if($borrowing->status === 'pending')
                                    <form method="POST" action="{{ route('staff.borrowings.approve', $borrowing) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-600 text-white hover:bg-green-700 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('staff.borrowings.reject', $borrowing) }}" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Reject Request', message: 'Provide a reason so the student knows why this request was rejected.', type: 'danger', requireReason: true, reasonLabel: 'Rejection reason', reasonPlaceholder: 'e.g. Item reserved for class, unavailable stock, etc.' })">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg text-red-700 bg-red-50 hover:bg-red-100 ring-1 ring-red-200/60 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Reject
                                        </button>
                                    </form>
                                @elseif($borrowing->status === 'approved')
                                    <form method="POST" action="{{ route('staff.borrowings.issue', $borrowing) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Issue
                                        </button>
                                    </form>
                                @elseif($borrowing->status === 'issued')
                                    <form method="POST" action="{{ route('staff.borrowings.return', $borrowing) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="return_condition" value="good">
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-purple-600 text-white hover:bg-purple-700 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                            Return
                                        </button>
                                    </form>
                                @endif

                                @if($archived === '1')
                                    <form method="POST" action="{{ route('admin.borrowings.unarchive', $borrowing) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 ring-1 ring-blue-200/60 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            Restore
                                        </button>
                                    </form>
                                @elseif(in_array($borrowing->status, ['returned', 'rejected', 'cancelled']))
                                    <form method="POST" action="{{ route('admin.borrowings.archive', $borrowing) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg text-gray-600 bg-gray-50 hover:bg-gray-100 ring-1 ring-gray-200/60 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                            Archive
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p>No borrowing records found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($borrowings->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $borrowings->appends(['status' => $status, 'search' => $search, 'archived' => $archived])->links() }}
        </div>
        @endif
    </div>
</div>

<script>
    function borrowingSearchComponent() {
        return {
            query: '{{ $search }}',
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
                        const userEl = row.querySelector('td:first-child .font-semibold');
                        const itemEl = row.querySelector('td:nth-child(2) .font-medium');
                        const user = userEl ? userEl.textContent.trim() : '';
                        const item = itemEl ? itemEl.textContent.trim() : '';
                        const key = user + '|' + item;
                        if (!seen.has(key)) {
                            seen.add(key);
                            results.push({ name: user, detail: item });
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
</script>
@endsection
