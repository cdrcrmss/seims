@extends('layouts.app')

@section('title', 'Room QR Code - ' . $room->name)

@section('content')
<div class="max-w-lg mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Room QR Code</h1>
            <p class="text-gray-600">{{ $room->name }}</p>
        </div>
        <a href="{{ route('reservations.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            &larr; Back
        </a>
    </div>

    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-8 text-center">
        <div class="inline-block p-4 bg-white rounded-xl shadow-md ring-1 ring-gray-100">
            <img src="{{ $qrImageUrl }}" alt="QR Code for {{ $room->name }}" class="w-64 h-64">
        </div>

        <div class="mt-6 space-y-2">
            <h2 class="text-xl font-bold text-gray-900">{{ $room->name }}</h2>
            @if($room->building)
            <p class="text-sm text-gray-500">{{ $room->building }}</p>
            @endif
            <p class="text-xs text-gray-400 font-mono bg-gray-50 inline-block px-3 py-1 rounded-lg">{{ $roomCode }}</p>
        </div>

        <p class="text-sm text-gray-500 mt-4">
            Students scan this QR code to check in to their room reservation.
        </p>

        <div class="mt-6 flex justify-center gap-3">
            <button onclick="window.print()" class="bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 rounded-xl font-semibold transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print
            </button>
        </div>
    </div>
</div>
@endsection
