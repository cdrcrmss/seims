@extends('layouts.app')

@section('title', 'My Borrowings')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">My Borrowings</h1>
            <p class="text-gray-600">View and manage your borrowing requests</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('student.borrow.form') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                New Request
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    @php
        $allMyBorrowings = \App\Models\Borrowing::where('user_id', auth()->id())->get();
        $cardStats = [
            ['label' => 'Pending', 'count' => $allMyBorrowings->where('status', 'pending')->count(), 'filter' => 'pending', 'color' => 'amber'],
            ['label' => 'Approved', 'count' => $allMyBorrowings->where('status', 'approved')->count(), 'filter' => 'approved', 'color' => 'teal'],
            ['label' => 'Active', 'count' => $allMyBorrowings->where('status', 'issued')->count(), 'filter' => 'issued', 'color' => 'green'],
            ['label' => 'Returned', 'count' => $allMyBorrowings->where('status', 'returned')->count(), 'filter' => 'returned', 'color' => 'emerald'],
        ];
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        @foreach($cardStats as $card)
        <a href="{{ route('student.borrowings.index', ['status' => $card['filter']]) }}" class="bg-white rounded-2xl p-6 ring-1 {{ ($status ?? '') === $card['filter'] ? 'ring-2 ring-' . $card['color'] . '-400' : 'ring-gray-100' }} shadow-sm card-hover cursor-pointer hover:ring-{{ $card['color'] }}-200 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">{{ $card['label'] }}</p>
                    <p class="text-3xl font-bold text-{{ $card['color'] }}-600 font-poppins">{{ $card['count'] }}</p>
                </div>
                <div class="w-12 h-12 bg-{{ $card['color'] }}-500/20 rounded-xl flex items-center justify-center">
                    @if($card['filter'] === 'pending')
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    @elseif($card['filter'] === 'approved')
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    @elseif($card['filter'] === 'issued')
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    @elseif($card['filter'] === 'returned')
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                    @endif
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <!-- Borrowing List -->
    <div class="bg-white rounded-2xl ring-1 ring-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-xl font-bold text-gray-900">
                    @if($status ?? false)
                        {{ ucfirst($status === 'issued' ? 'Active' : $status) }} Borrowings
                    @else
                        All Borrowings
                    @endif
                </h2>
                @if($status ?? false)
                    <a href="{{ route('student.borrowings.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Clear Filter
                    </a>
                @endif
            </div>
        </div>

        <div class="divide-y divide-white/10">
            @forelse($borrowings as $borrowing)
                <div class="p-6 hover:bg-white/5 transition-colors">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <!-- Request Info -->
                        <div class="flex-1">
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

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm mt-3">
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
                                                <p class="font-medium text-gray-900">{{ $borrowing->expected_return_date->format('M d, Y g:i A') }}</p>
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

                                    @if($borrowing->status === 'rejected' && $borrowing->rejection_reason)
                                        <div class="mt-3 p-3 bg-red-50 rounded-lg border border-red-200">
                                            <p class="text-sm text-red-800">
                                                <span class="font-medium">Rejection Reason:</span> {{ $borrowing->rejection_reason }}
                                            </p>
                                            @if($borrowing->rejector)
                                                <p class="text-xs text-red-600 mt-1">Rejected by {{ $borrowing->rejector->name }} on {{ $borrowing->rejected_date?->format('M d, Y') }}</p>
                                            @endif
                                        </div>
                                    @endif

                                    @if($borrowing->approver && in_array($borrowing->status, ['approved', 'issued', 'returned']))
                                        <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                            <p class="text-sm text-blue-800">
                                                <span class="font-medium">Approved by:</span> {{ $borrowing->approver->name }}
                                                @if($borrowing->approved_date) on {{ $borrowing->approved_date->format('M d, Y') }}@endif
                                            </p>
                                        </div>
                                    @endif

                                    @if($borrowing->status === 'issued' && $borrowing->issued_date)
                                        <div class="mt-3 p-3 bg-green-50 rounded-lg border border-green-200">
                                            <p class="text-sm text-green-800">
                                                <span class="font-medium">Issued on:</span> {{ $borrowing->issued_date->format('M d, Y') }}
                                                @if($borrowing->issuer) by {{ $borrowing->issuer->name }}@endif
                                            </p>
                                        </div>
                                    @endif

                                    @if($borrowing->status === 'returned' && $borrowing->returned_date)
                                        <div class="mt-3 p-3 bg-purple-50 rounded-lg border border-purple-200">
                                            <p class="text-sm text-purple-800">
                                                <span class="font-medium">Returned on:</span> {{ $borrowing->returned_date->format('M d, Y') }}
                                                @if($borrowing->returnedToUser) to {{ $borrowing->returnedToUser->name }}@endif
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap gap-2.5 lg:flex-col lg:items-end">
                            @if($borrowing->status === 'pending')
                                <form method="POST" action="{{ route('student.borrowings.cancel', $borrowing) }}" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { form: $el, title: 'Cancel Request', message: 'Are you sure you want to cancel this borrow request?', type: 'danger' })">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl bg-gradient-to-r from-red-500 to-rose-600 text-white hover:from-red-600 hover:to-rose-700 shadow-md shadow-red-200/50 hover:shadow-lg hover:shadow-red-300/50 transition-all duration-200 hover:-translate-y-0.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Cancel Request
                                    </button>
                                </form>
                            @endif

                            @if($borrowing->status === 'issued')
                                <div class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 text-amber-700 ring-1 ring-amber-200/80 shadow-sm">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Return by {{ $borrowing->expected_return_date->format('M d, Y g:i A') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No borrowing history</h3>
                        <p class="text-gray-600 mb-4">You haven't made any borrowing requests yet.</p>
                        <a href="{{ route('student.borrow.form') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Browse Items
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($borrowings->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $borrowings->appends(['status' => $status ?? ''])->links() }}
            </div>
        @endif
    </div>
</div>

@include('partials.borrow-cart-clear-script', ['cartStorageKeys' => ['seims_borrow_cart_student']])
@endsection
