<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#16a34a">

    <title>{{ config('app.name', 'SEIMS') }} - @yield('title', 'Laboratory Management')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/spup_logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/spup_logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/spup_logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js (pinned version) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    
    @stack('styles')

    <style>
        html, body {
            margin: 0 !important;
            padding: 0 !important;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            scroll-behavior: smooth;
        }
        .font-poppins {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        [x-cloak] { display: none !important; }

        /* Note: .btn-primary, .card-hover, .glass, .animate-* classes are defined in resources/css/app.css */

        /* Modern scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

        /* Sidebar scrollbar */
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 2px; }

        /* Ensure sidebar stays visible and fixed full-height on desktop */
        @media (min-width: 1024px) {
            aside {
                transform: translateX(0) !important;
                translate: 0 !important;
            }
            #main-content-area {
                margin-left: 16rem; /* w-64 = 256px = 16rem */
            }
        }
        /* On mobile, no margin since sidebar is an overlay */
        @media (max-width: 1023px) {
            #main-content-area {
                margin-left: 0 !important;
            }
        }

        /* Pulse animation for badges */
        @keyframes pulse-green {
            0%, 100% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.4); }
            50% { box-shadow: 0 0 0 6px rgba(22, 163, 74, 0); }
        }
        .animate-pulse-green {
            animation: pulse-green 2s ease-in-out infinite;
        }

        /* Active sidebar link */
        .sidebar-link-active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            font-weight: 600;
        }
        .sidebar-link {
            color: rgba(255, 255, 255, 0.65);
            transition: all 0.2s ease;
        }
        .sidebar-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="font-inter antialiased bg-white">
    <!-- Skip to content link for keyboard/screen reader users -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-[200] focus:bg-green-600 focus:text-white focus:px-4 focus:py-2 focus:rounded-lg focus:text-sm focus:font-semibold">Skip to main content</a>

    <div class="h-screen overflow-hidden">

        {{-- ============================================================ --}}
        {{-- LEFT SIDEBAR � always visible on desktop, overlay on mobile  --}}
        {{-- ============================================================ --}}

        {{-- Mobile overlay backdrop --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden"></div>

        {{-- Sidebar panel --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-green-700 transform transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col shadow-xl"
               role="navigation" aria-label="Main navigation">
            
            {{-- Logo area --}}
            <div class="flex items-center justify-between px-5 py-5 border-b border-white/10">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/spup_logo.png') }}" alt="SPUP Logo" class="w-9 h-9 object-contain rounded-lg bg-white/10 p-0.5">
                    <h1 class="text-xl font-bold text-white font-poppins tracking-tight">SEIMS</h1>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-lg text-white/70 hover:bg-white/10 transition-colors" aria-label="Close sidebar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Navigation links --}}
            <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto sidebar-scroll">
                {{-- Main --}}
                <p class="px-3 mb-2 text-[10px] font-bold text-white/40 uppercase tracking-widest">Main</p>

                <a href="{{ route('dashboard') }}"
                   class="flex items-center px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"></path></svg>
                    Dashboard
                </a>

                @if(auth()->user()->isStaff() || auth()->user()->isAdmin())
                <a href="{{ route('staff.items.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('staff.items.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    Items
                </a>
                <a href="{{ route('staff.borrowings.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('staff.borrowings.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Requests
                </a>
                <a href="{{ route('staff.borrow.form') }}"
                   class="flex items-center px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('staff.borrow.form') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"></path></svg>
                    Borrow Items
                </a>
                @endif

                @if(auth()->user()->role === 'student')
                <a href="{{ route('student.borrow.form') }}"
                   class="flex items-center px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('student.borrow.form') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"></path></svg>
                    Borrow Items
                </a>
                <a href="{{ route('student.borrowings.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('student.borrowings.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    My Borrowings
                </a>
                @endif

                {{-- Modules --}}
                @if(auth()->user()->isStaff() || auth()->user()->isAdmin())
                <p class="px-3 mt-5 mb-2 text-[10px] font-bold text-white/40 uppercase tracking-widest">Modules</p>

                <a href="{{ route('reservations.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('reservations.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Reservations
                </a>

                <a href="{{ route('maintenance.dashboard') }}"
                   class="flex items-center px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('maintenance.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Maintenance
                </a>
                <a href="{{ route('analytics.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('analytics.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Analytics
                </a>
                <a href="{{ route('qr.scanner') }}"
                   class="flex items-center px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('qr.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    QR Scanner
                </a>
                @endif

                {{-- Admin section --}}
                @if(auth()->user()->isAdmin())
                <p class="px-3 mt-5 mb-2 text-[10px] font-bold text-white/40 uppercase tracking-widest">Admin</p>

                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.users.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>
                    Users
                </a>
                <a href="{{ route('admin.borrowings') }}"
                   class="flex items-center px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.borrowings') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    All Borrowings
                </a>
                <a href="{{ route('admin.reports') }}"
                   class="flex items-center px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.reports') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Reports
                </a>
                @endif
            </nav>

            {{-- Mobile sidebar footer: profile & sign out --}}
            <div class="flex-shrink-0 px-3 py-4 border-t border-white/10 lg:hidden">
                <div class="px-3 py-2 mb-2">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-white/50 truncate">{{ auth()->user()->email }}</p>
                </div>
                <a href="{{ route('profile') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm sidebar-link">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Profile Settings
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-3 py-2.5 rounded-xl text-sm text-red-200 hover:bg-white/10 transition-colors">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Sign out
                    </button>
                </form>
            </div>

        </aside>

        {{-- ============================================================ --}}
        {{-- MAIN CONTENT AREA                                            --}}
        {{-- ============================================================ --}}
        <div class="flex flex-col h-full overflow-hidden"
             id="main-content-area">

            {{-- Top bar (mobile hamburger + page title + quick actions) --}}
            <header class="flex-shrink-0 z-30 bg-white/80 backdrop-blur-xl border-b border-gray-100 shadow-sm">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    {{-- Left: mobile menu + page title --}}
                    <div class="flex items-center space-x-3 min-w-0 flex-1">
                        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors flex-shrink-0" aria-label="Open sidebar">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <h2 class="text-base sm:text-lg font-semibold text-gray-800 font-poppins truncate max-w-[9rem] sm:max-w-none">@yield('title', 'Dashboard')</h2>
                    </div>

                    {{-- Right: notification bell + mini avatar --}}
                    <div class="flex items-center space-x-3">
                        {{-- Notification dropdown --}}
                        <div x-data="{ notifOpen: false }" class="relative">
                            <button @click="notifOpen = !notifOpen" class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors group cursor-pointer" aria-label="Notifications">
                                <svg class="w-5 h-5 group-hover:text-green-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5V9a5 5 0 00-10 0v3l-5 5h5m0 0v1a3 3 0 006 0v-1m-3 0h3"></path>
                                </svg>
                                @if($unreadNotifCount > 0)
                                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-green-500 rounded-full animate-pulse-green"></span>
                                @endif
                            </button>
                            
                            {{-- Notification dropdown panel --}}
                            <div x-show="notifOpen" @click.away="notifOpen = false" x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50">
                                <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                                    <p class="text-sm font-semibold text-gray-900">Notifications</p>
                                    @if($unreadNotifCount > 0)
                                    <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">{{ $unreadNotifCount }} new</span>
                                    @endif
                                </div>
                                <div class="max-h-64 overflow-y-auto">
                                    @forelse($headerNotifications as $notif)
                                    <a href="{{ $notif->action_url ?? '#' }}" class="block px-4 py-3 hover:bg-gray-50 transition-colors {{ is_null($notif->read_at) ? 'bg-green-50/40' : '' }}">
                                        <div class="flex items-start space-x-3">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                                @switch($notif->type ?? 'info')
                                                    @case('success') bg-green-100 @break
                                                    @case('warning') bg-yellow-100 @break
                                                    @case('error') bg-red-100 @break
                                                    @default bg-blue-100
                                                @endswitch
                                            ">
                                                @switch($notif->type ?? 'info')
                                                    @case('success')
                                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        @break
                                                    @case('warning')
                                                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        @break
                                                    @case('error')
                                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        @break
                                                    @default
                                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @endswitch
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-gray-900 {{ is_null($notif->read_at) ? 'font-semibold' : '' }}">{{ $notif->title ?? $notif->message }}</p>
                                                @if($notif->title && $notif->message)
                                                <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $notif->message }}</p>
                                                @endif
                                                <p class="text-xs text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </a>
                                    @empty
                                    <div class="px-4 py-8 text-center">
                                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5V9a5 5 0 00-10 0v3l-5 5h5m0 0v1a3 3 0 006 0v-1m-3 0h3"></path></svg>
                                        <p class="text-sm text-gray-400">No notifications yet</p>
                                    </div>
                                    @endforelse
                                </div>
                                @if($headerNotifications->count() > 0)
                                <div class="border-t border-gray-100 px-4 py-2">
                                    <button @click="notifOpen = false" class="text-sm text-green-600 hover:text-green-700 font-medium">Close</button>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- User dropdown --}}
                        <div x-data="{ userOpen: false }" class="relative">
                            <button @click="userOpen = !userOpen" type="button" aria-label="Account menu" :aria-expanded="userOpen" class="flex items-center space-x-2 pl-2 border-l border-gray-200 cursor-pointer hover:bg-gray-50 rounded-lg pr-2 py-1 transition-colors">
                                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="text-sm text-gray-700 font-medium hidden md:inline max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-400 hidden sm:block transition-transform flex-shrink-0" :class="userOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            {{-- User dropdown panel --}}
                            <div x-show="userOpen" @click.away="userOpen = false" x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-56 max-w-[calc(100vw-2rem)] bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full capitalize">{{ auth()->user()->role }}</span>
                                </div>
                                <a href="{{ route('profile') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Profile Settings
                                </a>
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    System Settings
                                </a>
                                @endif
                                <div class="border-t border-gray-100 mt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                            Sign out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page content --}}
            <main id="main-content" class="flex-1 p-4 sm:p-5 lg:p-6 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- ============================================== --}}
    {{-- GLOBAL CONFIRM MODAL (replaces native confirm) --}}
    {{-- ============================================== --}}
    <div x-data="{
            open: false,
            title: '',
            message: '',
            type: 'warning',
            pendingForm: null,
            show(detail) {
                this.title = detail.title || 'Confirm Action';
                this.message = detail.message || 'Are you sure you want to proceed?';
                this.type = detail.type || 'warning';
                this.pendingForm = detail.form || null;
                this.open = true;
            },
            proceed() {
                if (this.pendingForm) {
                    this.pendingForm.submit();
                }
                this.open = false;
                this.pendingForm = null;
            },
            cancel() {
                this.open = false;
                this.pendingForm = null;
            }
         }"
         @open-confirm-modal.window="show($event.detail)"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="cancel()"></div>

        {{-- Modal Panel --}}
        <div class="relative bg-white rounded-2xl shadow-2xl p-6 transform transition-all" style="width: 360px; max-width: 90vw;"
             x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-90 translate-y-4"
             @click.stop>
            <div class="flex flex-col items-center text-center">
                {{-- Success Icon --}}
                <template x-if="type === 'success'">
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mb-4 ring-4 ring-green-50">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </template>
                {{-- Danger Icon --}}
                <template x-if="type === 'danger'">
                    <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-4 ring-4 ring-red-50">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </div>
                </template>
                {{-- Warning Icon --}}
                <template x-if="type === 'warning'">
                    <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center mb-4 ring-4 ring-amber-50">
                        <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </template>

                <h3 class="text-lg font-bold text-gray-900 font-poppins mb-1" x-text="title"></h3>
                <p class="text-sm text-gray-500 mb-6 leading-relaxed" x-text="message"></p>

                <div class="flex gap-3 w-full">
                    <button @click="cancel()" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                    <button @click="proceed()"
                            class="flex-1 px-4 py-2.5 text-white text-sm font-semibold rounded-xl transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                            :class="{
                                'bg-green-600 hover:bg-green-700 focus:ring-green-500': type === 'success',
                                'bg-red-600 hover:bg-red-700 focus:ring-red-500': type === 'danger',
                                'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500': type === 'warning'
                            }">
                        <span x-text="type === 'danger' ? 'Delete' : 'Confirm'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')

    {{-- ============================================== --}}
    {{-- TOAST NOTIFICATIONS (modern centered style)    --}}
    {{-- ============================================== --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-cloak
             class="fixed inset-0 z-[90] flex items-center justify-center p-4 pointer-events-none" role="alert">
            <div class="pointer-events-auto bg-white rounded-2xl shadow-2xl ring-1 ring-gray-100 p-6 text-center transform" style="width: 360px; max-width: 90vw;"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-90 translate-y-4">
                <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3 ring-4 ring-green-50">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h4 class="text-base font-bold text-gray-900 font-poppins mb-1">Success</h4>
                <p class="text-sm text-gray-500">{{ session('success') }}</p>
                <button @click="show = false" class="mt-4 px-5 py-2 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition-colors w-full">OK</button>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-cloak
             class="fixed inset-0 z-[90] flex items-center justify-center p-4 pointer-events-none" role="alert">
            <div class="pointer-events-auto bg-white rounded-2xl shadow-2xl ring-1 ring-gray-100 p-6 text-center transform" style="width: 360px; max-width: 90vw;"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-90 translate-y-4">
                <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3 ring-4 ring-blue-50">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h4 class="text-base font-bold text-gray-900 font-poppins mb-1">Information</h4>
                <p class="text-sm text-gray-500">{{ session('info') }}</p>
                <button @click="show = false" class="mt-4 px-5 py-2 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-colors w-full">OK</button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-cloak
             class="fixed inset-0 z-[90] flex items-center justify-center p-4 pointer-events-none" role="alert">
            <div class="pointer-events-auto bg-white rounded-2xl shadow-2xl ring-1 ring-gray-100 p-6 text-center transform" style="width: 360px; max-width: 90vw;"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-90 translate-y-4">
                <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3 ring-4 ring-red-50">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h4 class="text-base font-bold text-gray-900 font-poppins mb-1">Error</h4>
                <p class="text-sm text-gray-500">{{ session('error') }}</p>
                <button @click="show = false" class="mt-4 px-5 py-2 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition-colors w-full">OK</button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div x-data="{ show: true }" x-show="show" x-cloak
             class="fixed inset-0 z-[90] flex items-center justify-center p-4 pointer-events-none">
            <div class="fixed inset-0 bg-gray-900/30 backdrop-blur-sm pointer-events-auto" @click="show = false"></div>
            <div class="pointer-events-auto relative bg-white rounded-2xl shadow-2xl ring-1 ring-gray-100 p-6 text-center transform" style="width: 360px; max-width: 90vw;"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-90 translate-y-4">
                <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3 ring-4 ring-red-50">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <h4 class="text-base font-bold text-gray-900 font-poppins mb-2">Something went wrong</h4>
                <div class="text-sm text-gray-500 space-y-1">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
                <button @click="show = false" class="mt-4 px-5 py-2 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition-colors w-full">OK</button>
            </div>
        </div>
    @endif
</body>
</html>