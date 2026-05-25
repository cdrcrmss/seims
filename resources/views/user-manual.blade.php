@extends('layouts.app')

@section('title', 'User Guide')

@php
    $sectionIcons = [
        'getting-started' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
        'students' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>',
        'staff' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
        'administrators' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
        'tips' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>',
        'need-help' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    ];
    $defaultIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>';
@endphp

@section('content')
<div class="space-y-6 max-w-6xl mx-auto" x-data="{ activeSection: window.location.hash ? window.location.hash.slice(1) : '{{ $sections[0]['id'] ?? '' }}' }">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-green-600 mb-1">Documentation</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 font-poppins">User Guide</h1>
            <p class="mt-1 text-sm text-gray-500 max-w-xl">Borrowing, inventory, reservations, and maintenance in SEIS</p>
        </div>
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center justify-center gap-2 bg-white ring-1 ring-gray-200 hover:ring-gray-300 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-semibold shadow-sm transition-all shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Dashboard
        </a>
    </div>

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-700 via-green-600 to-emerald-700 text-white shadow-lg shadow-green-900/10">
        <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-white/10 blur-2xl pointer-events-none" aria-hidden="true"></div>
        <div class="absolute -left-4 bottom-0 w-32 h-32 rounded-full bg-white/5 blur-xl pointer-events-none" aria-hidden="true"></div>
        <div class="relative px-6 py-8 sm:px-8 sm:py-10">
            <div class="manual-intro prose prose-invert prose-sm sm:prose-base max-w-none prose-headings:font-poppins prose-headings:text-white prose-headings:mb-2 prose-p:text-green-50 prose-p:leading-relaxed prose-strong:text-white prose-a:text-green-100">
                {!! $introHtml !!}
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">
        @if(count($sections) > 0)
        <nav class="lg:w-56 shrink-0" aria-label="Guide sections">
            <div class="lg:sticky lg:top-24 bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm p-4">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 px-2 mb-3">On this page</p>
                <ul class="space-y-0.5">
                    @foreach($sections as $section)
                    <li>
                        <a href="#{{ $section['id'] }}"
                           @click="activeSection = '{{ $section['id'] }}'"
                           :class="activeSection === '{{ $section['id'] }}' ? 'bg-green-50 text-green-800 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                           class="block rounded-lg px-3 py-2 text-sm transition-colors">
                            {{ $section['title'] }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </nav>
        @endif

        <div class="flex-1 min-w-0 space-y-5">
            @foreach($sections as $section)
            @php $iconPath = $sectionIcons[$section['id']] ?? $defaultIcon; @endphp
            <section id="{{ $section['id'] }}"
                     class="scroll-mt-24 bg-white rounded-2xl ring-1 ring-gray-200 shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-5 sm:px-6 py-4 border-b border-gray-100 bg-gray-50/80">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">{!! $iconPath !!}</svg>
                    </span>
                    <h2 class="text-lg font-bold text-gray-900 font-poppins">{{ $section['title'] }}</h2>
                </div>
                <div class="manual-section px-5 sm:px-6 py-5 sm:py-6 prose prose-sm sm:prose-base max-w-none
                    prose-headings:font-poppins prose-headings:text-gray-900
                    prose-h3:text-base prose-h3:font-semibold prose-h3:mt-6 prose-h3:mb-2
                    prose-p:text-gray-600 prose-p:leading-relaxed
                    prose-li:text-gray-600 prose-li:marker:text-green-600
                    prose-strong:text-gray-800 prose-strong:font-semibold
                    prose-a:text-green-700 prose-a:font-medium hover:prose-a:text-green-800
                    prose-hr:hidden
                    prose-table:rounded-xl prose-table:overflow-hidden prose-table:ring-1 prose-table:ring-gray-200
                    prose-thead:bg-gray-50 prose-th:text-left prose-th:text-xs prose-th:uppercase prose-th:tracking-wide prose-th:text-gray-500 prose-th:font-semibold prose-th:px-4 prose-th:py-3
                    prose-td:px-4 prose-td:py-3 prose-td:text-sm prose-td:text-gray-700
                    prose-tr:border-b prose-tr:border-gray-100">
                    {!! preg_replace('/<h2[^>]*>.*?<\/h2>\s*/i', '', $section['html'], 1) !!}
                </div>
            </section>
            @endforeach
        </div>
    </div>
</div>
@endsection
