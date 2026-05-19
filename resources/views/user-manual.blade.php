@extends('layouts.app')

@section('title', 'User Guide')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-poppins">User Guide / Manual</h1>
            <p class="text-gray-600">How to use SEIMS for borrowing, inventory, reservations, and maintenance</p>
        </div>
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Dashboard
        </a>
    </div>

    <div class="bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-6 sm:p-8 max-w-4xl">
        <article class="prose prose-green prose-sm sm:prose-base max-w-none prose-headings:font-poppins prose-a:text-green-700">
            {!! $content !!}
        </article>
    </div>
</div>
@endsection
