@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900 font-poppins">Welcome back, {{ auth()->user()->name }}</h1>
        <p class="text-sm text-gray-500">{{ now()->format('l, F d, Y') }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <x-stat-card label="Total Items" :value="$totalItems" color="neutral" :href="route('staff.items.index')"
            icon="<svg class='w-5 h-5 text-slate-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'></path></svg>" />

        <x-stat-card label="Low Stock" :value="$lowStockItems" color="neutral" :href="route('staff.items.index', ['stock_filter' => 'low_stock'])"
            icon="<svg class='w-5 h-5 text-slate-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'></path></svg>" />

        <x-stat-card label="Pending" :value="$pendingRequests" color="neutral" :href="route('staff.borrowings.index', ['status' => 'pending'])"
            icon="<svg class='w-5 h-5 text-slate-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'></path></svg>" />

        <x-stat-card label="Overdue" :value="$overdueItems" color="danger" :href="route('staff.borrowings.index', ['status' => 'issued'])"
            icon="<svg class='w-5 h-5 text-rose-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'></path></svg>" />
    </div>

    <!-- Alerts -->
    @if($overdueItems > 0 || $maintenanceDue > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @if($overdueItems > 0)
            <x-alert-banner type="danger" :message="$overdueItems . ' Overdue Return' . ($overdueItems > 1 ? 's' : '')" :action-url="route('staff.borrowings.index')" action-label="View" />
        @endif
        @if($maintenanceDue > 0)
            <x-alert-banner type="warning" :message="$maintenanceDue . ' Maintenance Due'" :action-url="route('maintenance.dashboard')" action-label="View" />
        @endif
    </div>
    @endif

    <!-- Pending Borrow Requests -->
    <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900 font-poppins">Pending Borrow Requests</h2>
            <a href="{{ route('staff.borrowings.index') }}" class="text-xs font-semibold text-green-600 hover:text-green-700">View All &rarr;</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($pendingBorrowings as $borrowing)
            <div class="px-5 py-3.5 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 text-green-700 text-xs font-bold">
                            {{ strtoupper(substr($borrowing->user?->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-900 text-sm truncate">{{ $borrowing->user?->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $borrowing->item?->name ?? 'Unknown' }} &middot; Qty: {{ $borrowing->quantity }} &middot; {{ $borrowing->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <form method="POST" action="{{ route('staff.borrowings.approve', $borrowing) }}" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Approve Request', message: 'Are you sure you want to approve this borrow request?', type: 'success' })">
                            @csrf @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-50 text-green-700 hover:bg-green-100 ring-1 ring-green-200/60 transition-all duration-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('staff.borrowings.reject', $borrowing) }}" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Reject Request', message: 'Are you sure you want to reject this borrow request?', type: 'danger' })">
                            @csrf @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-50 text-red-700 hover:bg-red-100 ring-1 ring-red-200/60 transition-all duration-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reject
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center">
                <p class="text-sm text-gray-400">No pending requests</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Overdue Borrowings -->
    @if($overdueBorrowings->count() > 0)
    <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900 font-poppins">Overdue Borrowings</h2>
            <a href="{{ route('staff.borrowings.index') }}" class="text-xs font-semibold text-red-600 hover:text-red-700">Manage &rarr;</a>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($overdueBorrowings as $borrowing)
            <div class="px-5 py-3 hover:bg-gray-50 transition-colors flex items-center justify-between gap-4">
                <div class="flex items-center space-x-3 min-w-0">
                    <div class="w-7 h-7 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0 text-red-600 text-xs font-bold">
                        {{ strtoupper(substr($borrowing->user?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $borrowing->user?->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $borrowing->item?->name ?? 'Unknown' }}</p>
                    </div>
                </div>
                <span class="text-xs font-semibold text-red-600 whitespace-nowrap">Due {{ $borrowing->expected_return_date->diffForHumans() }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
