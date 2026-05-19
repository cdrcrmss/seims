@extends('layouts.app')

@section('title', 'Request Items')

@section('content')
<div class="space-y-6" x-data="borrowForm()">

    <div class="flex items-center gap-4">
        <a href="{{ route('student.borrowings.index') }}" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-poppins">Request Items</h1>
            <p class="text-sm text-gray-500">Select items and submit a borrow request for staff approval</p>
        </div>
    </div>

    @if($hasOverdue)
    <div class="flex items-center gap-3 bg-red-50 rounded-xl p-4 ring-1 ring-red-200">
        <p class="text-sm font-semibold text-red-800">Return overdue items before submitting new requests.</p>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 rounded-xl p-4 ring-1 ring-red-200 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 ring-1 ring-red-200 text-red-700 rounded-xl p-4 text-sm">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <div class="lg:col-span-3 space-y-4">
            @include('partials.borrow-items-browser', [
                'filterRoute' => route('student.borrow.form'),
                'availableItems' => $availableItems,
                'categories' => $categories,
                'search' => $search,
                'category' => $category,
            ])
        </div>

        <div class="lg:col-span-2">
            @include('partials.borrow-multi-cart', [
                'formTitle' => 'Borrowing Request',
                'formSubtitle' => 'Add items, set return time, and submit for approval',
                'formAction' => route('student.borrow'),
                'submitLabel' => 'Submit Request',
                'cancelUrl' => route('student.borrowings.index'),
                'showPurpose' => true,
                'isRequestMode' => true,
            ])
        </div>
    </div>
</div>

@include('partials.borrow-form-alpine', [
    'selectedItem' => $selectedItem,
    'search' => $search,
    'category' => $category,
    'hasOverdue' => $hasOverdue,
    'requirePurpose' => true,
    'submitLabel' => 'Submit Request',
    'cartStorageKey' => 'seims_borrow_cart_student',
    'searchApiUrl' => route('student.api.search-items'),
    'filterBaseUrl' => route('student.borrow.form'),
])
@endsection
