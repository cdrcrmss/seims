@extends('layouts.app')

@section('title', 'QR Code - ' . $item->name)

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center space-x-4 animate-fade-in-up">
        <a href="{{ route('qr.scanner') }}" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">QR Code</h1>
            <p class="text-gray-600">{{ $item->name }}</p>
        </div>
    </div>

    <div class="max-w-lg mx-auto">
        <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-8 text-center animate-fade-in-up stagger-1">
            <!-- QR Code Image -->
            <div class="inline-block bg-white p-4 rounded-2xl ring-1 ring-gray-100 mb-6">
                <img src="{{ $qrImageUrl }}" alt="QR Code for {{ $item->name }}" class="w-64 h-64 mx-auto">
            </div>

            <!-- Item Info -->
            <h2 class="text-xl font-bold text-gray-900">{{ $item->name }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $item->category }}</p>
            
            <div class="mt-4 inline-flex items-center px-4 py-2 bg-gray-100 rounded-xl">
                <span class="text-sm font-mono font-semibold text-gray-700">{{ $item->qr_code }}</span>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-4 text-left">
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 uppercase">Stock</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $item->available_stock }} / {{ $item->total_stock }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 uppercase">Status</p>
                    <p class="text-sm font-semibold {{ $item->available_stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $item->available_stock > 0 ? 'Available' : 'Out of Stock' }}
                    </p>
                </div>
            </div>

            <!-- Print Button -->
            <div class="mt-6 flex space-x-3 justify-center">
                <button onclick="window.print()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-xl font-semibold transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Print QR Code</span>
                </button>
                <a href="{{ route('qr.scanner') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl font-semibold transition-all duration-200">
                    Back to Scanner
                </a>
            </div>

            <p class="mt-4 text-xs text-gray-400">Scan URL: {{ $qrData }}</p>
        </div>
    </div>
</div>
@endsection
