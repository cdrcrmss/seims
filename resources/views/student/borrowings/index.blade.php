@extends('layouts.app')

@section('title', 'Borrowing History')

@section('content')
@php
    $currentFilter = $status ?? '';
    $statusStyles = [
        'pending' => 'bg-amber-50 text-amber-800 ring-amber-200/80',
        'approved' => 'bg-sky-50 text-sky-800 ring-sky-200/80',
        'issued' => 'bg-green-50 text-green-800 ring-green-200/80',
        'returned' => 'bg-violet-50 text-violet-800 ring-violet-200/80',
        'rejected' => 'bg-red-50 text-red-800 ring-red-200/80',
    ];
    $statusLabels = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'issued' => 'Active',
        'returned' => 'Returned',
        'rejected' => 'Rejected',
    ];
@endphp

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-green-600 mb-1">Student</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 font-poppins">Borrowing History</h1>
            <p class="text-sm text-gray-500 mt-1">Track your requests, active loans, and past returns</p>
        </div>
        <a href="{{ route('student.borrow.form') }}"
           class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            New Request
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
        <x-stat-card label="Pending" :value="$statusCounts['pending']" color="yellow"
            :href="route('student.borrowings.index', ['status' => 'pending'])"
            icon="<svg class='w-6 h-6 text-yellow-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'/></svg>" />
        <x-stat-card label="Approved" :value="$statusCounts['approved']" color="blue"
            :href="route('student.borrowings.index', ['status' => 'approved'])"
            icon="<svg class='w-6 h-6 text-blue-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7'/></svg>" />
        <x-stat-card label="Active" :value="$statusCounts['issued']" color="green"
            :href="route('student.borrowings.index', ['status' => 'issued'])"
            icon="<svg class='w-6 h-6 text-green-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'/></svg>" />
        <x-stat-card label="Returned" :value="$statusCounts['returned']" color="purple"
            :href="route('student.borrowings.index', ['status' => 'returned'])"
            icon="<svg class='w-6 h-6 text-purple-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6'/></svg>" />
        <x-stat-card label="Rejected" :value="$statusCounts['rejected']" color="red"
            :href="route('student.borrowings.index', ['status' => 'rejected'])"
            icon="<svg class='w-6 h-6 text-red-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 18L18 6M6 6l12 12'/></svg>" />
    </div>

    <div class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <h2 class="text-base font-bold text-gray-900 font-poppins">
                @if($currentFilter)
                    {{ $statusLabels[$currentFilter] ?? ucfirst($currentFilter) }} records
                @else
                    All records
                @endif
            </h2>
            <span class="text-xs text-gray-500">{{ $borrowings->total() }} total</span>
        </div>

        @forelse($borrowings as $borrowing)
        @php
            $badgeClass = $statusStyles[$borrowing->status] ?? 'bg-gray-50 text-gray-700 ring-gray-200';
            $badgeLabel = $statusLabels[$borrowing->status] ?? ucfirst($borrowing->status);
            $purpose = $borrowing->notes && str_starts_with($borrowing->notes, 'Purpose: ')
                ? trim(substr($borrowing->notes, 9))
                : $borrowing->notes;
        @endphp
        <article class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden hover:ring-green-100 transition-all">
            <div class="p-4 sm:p-5">
                <div class="flex gap-4">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl bg-gray-100 ring-1 ring-gray-200/80 overflow-hidden flex-shrink-0">
                        @if($borrowing->item?->image_path)
                            <img src="{{ $borrowing->item->image_url }}" alt="" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                            <h3 class="text-base font-semibold text-gray-900 truncate pr-2">
                                {{ $borrowing->item?->name ?? 'Deleted Item' }}
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold ring-1 shrink-0 {{ $badgeClass }}">
                                {{ $badgeLabel }}
                            </span>
                        </div>

                        <div class="flex flex-wrap gap-2 text-xs">
                            @if($borrowing->item?->laboratory)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-green-50 text-green-800 ring-1 ring-green-100">
                                <svg class="w-3.5 h-3.5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                {{ $borrowing->item->laboratory }}
                            </span>
                            @endif
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-gray-50 text-gray-700 ring-1 ring-gray-100">
                                <span class="text-gray-400">Qty</span> {{ $borrowing->quantity }}
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-gray-50 text-gray-700 ring-1 ring-gray-100">
                                <span class="text-gray-400">Requested</span> {{ $borrowing->created_at->format('M d, Y') }}
                            </span>
                            @if($borrowing->expected_return_date)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-gray-50 text-gray-700 ring-1 ring-gray-100">
                                <span class="text-gray-400">Due</span> {{ $borrowing->expected_return_date->format('M d, g:i A') }}
                            </span>
                            @endif
                        </div>

                        @if($purpose)
                        <p class="mt-2 text-sm text-gray-600 line-clamp-2">
                            <span class="font-medium text-gray-500">Purpose:</span> {{ $purpose }}
                        </p>
                        @endif
                    </div>
                </div>

                @if($borrowing->status === 'rejected')
                <div class="mt-3 flex items-start gap-2 text-xs text-red-700 bg-red-50 rounded-lg px-3 py-2 ring-1 ring-red-100">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>
                        <span class="font-semibold">Reason:</span>
                        {{ $borrowing->rejection_reason ?: 'No reason provided.' }}
                        @if($borrowing->rejector)
                            <span class="text-red-600/80"> &middot; by {{ $borrowing->rejector->name }}</span>
                        @endif
                    </span>
                </div>
                @endif

                @php
                    $timeline = [];
                    if ($borrowing->approver && in_array($borrowing->status, ['approved', 'issued', 'returned'])) {
                        $timeline[] = ['label' => 'Approved', 'detail' => $borrowing->approver->name . ($borrowing->approved_date ? ' · ' . $borrowing->approved_date->format('M d, Y') : ''), 'color' => 'sky'];
                    }
                    if ($borrowing->status === 'issued' && $borrowing->issued_date) {
                        $timeline[] = ['label' => 'Issued', 'detail' => ($borrowing->issuer?->name ?? 'Staff') . ' · ' . $borrowing->issued_date->format('M d, Y'), 'color' => 'green'];
                    }
                    if ($borrowing->status === 'returned' && $borrowing->returned_date) {
                        $timeline[] = ['label' => 'Returned', 'detail' => ($borrowing->returnedToUser?->name ?? 'Staff') . ' · ' . $borrowing->returned_date->format('M d, Y'), 'color' => 'violet'];
                    }
                @endphp
                @if(count($timeline) > 0)
                <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap gap-2">
                    @foreach($timeline as $event)
                    <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1.5 rounded-lg
                        {{ $event['color'] === 'sky' ? 'bg-sky-50 text-sky-800' : ($event['color'] === 'green' ? 'bg-green-50 text-green-800' : 'bg-violet-50 text-violet-800') }}">
                        <span class="font-semibold">{{ $event['label'] }}</span>
                        <span class="text-gray-500">{{ $event['detail'] }}</span>
                    </span>
                    @endforeach
                </div>
                @endif

                @if($borrowing->status === 'pending' || $borrowing->status === 'issued')
                <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap justify-end gap-2">
                    @if($borrowing->status === 'pending')
                    <form method="POST" action="{{ route('student.borrowings.cancel', $borrowing) }}" class="inline"
                          x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Cancel Request', message: 'Are you sure you want to cancel this borrow request?', type: 'danger' })">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-red-50 text-red-700 hover:bg-red-100 ring-1 ring-red-200/80 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel request
                        </button>
                    </form>
                    @endif
                    @if($borrowing->status === 'issued' && $borrowing->expected_return_date)
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-amber-50 text-amber-800 ring-1 ring-amber-200/80">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Return by {{ $borrowing->expected_return_date->format('M d, g:i A') }}
                    </span>
                    @endif
                </div>
                @endif
            </div>
        </article>
        @empty
        <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm px-6 py-14 text-center">
            <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-gray-50 flex items-center justify-center">
                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">No records yet</h3>
            <p class="text-sm text-gray-500 mb-5 max-w-sm mx-auto">
                @if($currentFilter)
                    No {{ strtolower($statusLabels[$currentFilter] ?? $currentFilter) }} borrowings in your history.
                @else
                    You have not made any borrowing requests yet.
                @endif
            </p>
            <a href="{{ route('student.borrow.form') }}"
               class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Browse items
            </a>
        </div>
        @endforelse
    </div>

    @if($borrowings->hasPages())
    <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm px-4 py-3">
        {{ $borrowings->appends(['status' => $status ?? ''])->links() }}
    </div>
    @endif
</div>

@include('partials.borrow-cart-clear-script', ['cartStorageKeys' => ['seims_borrow_cart_student']])
@endsection
