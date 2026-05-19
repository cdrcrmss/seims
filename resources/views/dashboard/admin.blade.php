@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-slate-900 font-poppins">Welcome back, {{ auth()->user()->name }}</h1>
        <p class="text-sm text-slate-500">{{ now()->format('l, F d, Y') }}</p>
    </div

    {{-- Neutral metric cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <a href="{{ route('admin.users.index') }}" class="bg-white rounded-2xl p-6 ring-1 ring-slate-200/80 shadow-sm card-hover relative overflow-hidden cursor-pointer hover:ring-slate-300 transition-all block">
            <div class="absolute top-0 left-0 w-0.5 h-full bg-slate-300 rounded-r-full"></div
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Users</p>
                    <p class="text-3xl font-bold text-slate-900 font-poppins mt-0.5">{{ $totalUsers }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $userBreakdown['admins'] }}A &middot; {{ $userBreakdown['staff'] }}S &middot; {{ $userBreakdown['students'] }}St</p>
                </div
                <div class="w-11 h-11 bg-slate-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>
                </div
            </div
        </a>

        <a href="{{ route('staff.items.index') }}" class="bg-white rounded-2xl p-6 ring-1 ring-slate-200/80 shadow-sm card-hover relative overflow-hidden cursor-pointer hover:ring-slate-300 transition-all block">
            <div class="absolute top-0 left-0 w-0.5 h-full bg-slate-300 rounded-r-full"></div
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Equipment</p>
                    <p class="text-3xl font-bold text-slate-900 font-poppins mt-0.5">{{ $totalItems }}</p>
                    @if($lowStockItems > 0)
                        <p class="text-xs text-amber-700/90 font-medium mt-1">{{ $lowStockItems }} low stock</p>
                    @else
                        <p class="text-xs text-slate-400 mt-1">All stocked</p>
                    @endif
                </div
                <div class="w-11 h-11 bg-slate-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div
            </div
        </a>

        <a href="{{ route('admin.borrowings', ['status' => 'issued']) }}" class="bg-white rounded-2xl p-6 ring-1 ring-slate-200/80 shadow-sm card-hover relative overflow-hidden cursor-pointer hover:ring-slate-300 transition-all block">
            <div class="absolute top-0 left-0 w-0.5 h-full bg-slate-300 rounded-r-full"></div
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Active Borrowings</p>
                    <p class="text-3xl font-bold text-slate-900 font-poppins mt-0.5">{{ $activeBorrowings }}</p>
                    <p class="text-xs text-slate-400 mt-1">of {{ $totalBorrowings }} total</p>
                </div
                <div class="w-11 h-11 bg-slate-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                </div
            </div
        </a>

        <a href="{{ route('staff.borrowings.index', ['status' => 'pending']) }}" class="bg-white rounded-2xl p-6 ring-1 ring-slate-200/80 shadow-sm card-hover relative overflow-hidden cursor-pointer hover:ring-slate-300 transition-all block">
            <div class="absolute top-0 left-0 w-0.5 h-full bg-slate-300 rounded-r-full"></div
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Pending</p>
                    <p class="text-3xl font-bold text-slate-900 font-poppins mt-0.5">{{ $pendingRequests + $pendingReservations }}</p>
                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                        @if($pendingRequests > 0)<span class="text-xs text-slate-500 font-medium">{{ $pendingRequests }} borrow</span>@endif
                        @if($pendingReservations > 0)<span class="text-xs text-slate-500 font-medium">{{ $pendingReservations }} reserv</span>@endif
                    </div
                </div
                <div class="w-11 h-11 bg-slate-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div
            </div
        </a>
    </div

    @if($overdueItems > 0 || $maintenanceDue > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @if($overdueItems > 0)
            <x-alert-banner type="danger" :message="$overdueItems . ' Overdue Return' . ($overdueItems > 1 ? 's' : '')" :action-url="route('admin.borrowings')" action-label="View" />
        @endif
        @if($maintenanceDue > 0)
            <x-alert-banner type="warning" :message="$maintenanceDue . ' Maintenance Due'" :action-url="route('maintenance.dashboard')" action-label="View" />
        @endif
    </div
    @endif

    <div class="bg-white rounded-xl ring-1 ring-slate-200/80 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900 font-poppins">Pending Requests</h2>
            <a href="{{ route('staff.borrowings.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">View All &rarr;</a>
        </div
        <div class="divide-y divide-slate-50">
            @forelse($recentActivity->where('status', 'pending')->take(5) as $activity)
            <div class="px-5 py-3.5 hover:bg-slate-50/80 transition-colors">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                        <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0 text-slate-600 text-xs font-bold">
                            {{ strtoupper(substr($activity->user?->name ?? 'U', 0, 1)) }}
                        </div
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-900 text-sm truncate">{{ $activity->user?->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ $activity->item?->name ?? 'Unknown' }} &middot; Qty: {{ $activity->quantity }} &middot; {{ $activity->created_at->diffForHumans() }}</p>
                        </div
                    </div
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <form method="POST" action="{{ route('staff.borrowings.approve', $activity) }}" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Approve Request', message: 'Are you sure you want to approve this borrow request?', type: 'success' })">
                            @csrf @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 ring-1 ring-slate-200/60 transition-all duration-200">
                                Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('staff.borrowings.reject', $activity) }}" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Reject Request', message: 'Are you sure you want to reject this borrow request?', type: 'danger' })">
                            @csrf @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 ring-1 ring-rose-200/60 transition-all duration-200">
                                Reject
                            </button>
                        </form>
                    </div
                </div
            </div
            @empty
            <div class="px-5 py-10 text-center">
                <p class="text-sm text-slate-400">No pending requests</p>
            </div
            @endforelse
        </div
    </div

    <div class="bg-white rounded-xl ring-1 ring-slate-200/80 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900 font-poppins">Recent Activity</h2>
            <a href="{{ route('admin.borrowings') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">All Borrowings &rarr;</a>
        </div
        <div class="divide-y divide-slate-50">
            @forelse($recentActivity->take(5) as $activity)
            <div class="px-5 py-3 hover:bg-slate-50/80 transition-colors flex items-center justify-between gap-4">
                <div class="flex items-center space-x-3 min-w-0">
                    <div class="w-7 h-7 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0 text-slate-600 text-xs font-bold">
                        {{ strtoupper(substr($activity->user?->name ?? 'U', 0, 1)) }}
                    </div
                    <p class="text-sm text-slate-700 truncate">
                        <span class="font-semibold text-slate-900">{{ $activity->user?->name ?? 'Unknown' }}</span>
                        &middot; {{ $activity->item?->name ?? 'Unknown' }}
                    </p>
                </div
                <div class="flex items-center space-x-2 flex-shrink-0">
                    <x-status-badge :status="$activity->status" />
                    <span class="text-xs text-slate-400 hidden sm:inline">{{ $activity->created_at->diffForHumans() }}</span>
                </div
            </div
            @empty
            <div class="px-5 py-10 text-center">
                <p class="text-sm text-slate-400">No recent activity</p>
            </div
            @endforelse
        </div
    </div
</div
@endsection
