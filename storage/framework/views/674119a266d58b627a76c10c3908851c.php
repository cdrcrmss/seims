<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: false }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }" style="background-color: #f9fafb;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'INNOTRACK')); ?> - <?php echo $__env->yieldContent('title', 'Laboratory Management'); ?></title>

    <!-- Prevent white flash - set background immediately -->
    <style>
        html { background-color: #f9fafb; }
        html.dark { background-color: #111827; }
    </style>
    <script>
        // Apply dark mode immediately to prevent flash
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
            document.documentElement.style.backgroundColor = '#111827';
        }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Instant.page - Prefetch links on hover for instant navigation -->
    <script src="https://instant.page/5.2.0" type="module" integrity="sha384-jnZyxPjiipYXnU4qVToe8howtBHyoA9rlG/gD3FzA6KFHp5sxsmRAGqiH+5xG0oL" data-intensity="mousedown"></script>
    
    <!-- Prefetch visible navigation links immediately -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Prefetch all navigation links in viewport
            const links = document.querySelectorAll('nav a[href], a[href^="/"]');
            links.forEach(link => {
                const href = link.getAttribute('href');
                if (href && href.startsWith('/') && !href.includes('#')) {
                    const prefetchLink = document.createElement('link');
                    prefetchLink.rel = 'prefetch';
                    prefetchLink.href = href;
                    document.head.appendChild(prefetchLink);
                }
            });
        });
    </script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'poppins': ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Ensure proper font loading */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .font-poppins {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        /* Glass morphism */
        .glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .dark .glass {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Card hover */
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        /* Button styles */
        .btn-primary {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(22, 163, 74, 0.3);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #15803d 0%, #166534 100%);
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(22, 163, 74, 0.4);
        }
        
        /* Navbar link hover effect */
        .nav-link {
            position: relative;
            transition: color 0.2s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #16a34a, #10b981);
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }
    </style>
</head>
<body class="font-inter antialiased transition-colors duration-300 bg-gray-50 dark:bg-gray-900">
    <!-- Animated Background -->
    <div class="fixed inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-green-50/30 via-white to-emerald-50/30 dark:from-gray-900 dark:via-gray-800 dark:to-green-900/50"></div>
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-green-400/10 dark:bg-green-600/10 rounded-full filter blur-3xl opacity-70 animate-pulse"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-emerald-400/10 dark:bg-emerald-600/10 rounded-full filter blur-3xl opacity-70 animate-pulse" style="animation-delay: 2s;"></div>
    </div>
    
    <div class="relative z-10 min-h-screen">
        
        <!-- Navigation -->
        <nav class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl border-b border-gray-200/20 dark:border-gray-700/30 sticky top-0 z-50 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center space-x-3">
                            <img src="<?php echo e(asset('images/spup_logo.png')); ?>" alt="SPUP Logo" class="w-10 h-10 object-contain">
                            <h1 class="text-2xl font-bold text-green-600 dark:text-green-400 font-poppins">
                                INNOTRACK
                            </h1>
                        </div>
                    </div>

                    <!-- Desktop Navigation Links -->
                    <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-8">
                        <a href="<?php echo e(route('dashboard')); ?>" class="group relative px-3 py-2 text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-green-600 dark:hover:text-green-400 transition-all duration-200">
                            <span class="relative z-10">Dashboard</span>
                            <div class="absolute inset-0 bg-green-500/10 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-200"></div>
                        </a>
                        <?php if(auth()->user()->isStaff() || auth()->user()->isAdmin()): ?>
                            <a href="<?php echo e(route('staff.items.index')); ?>" class="group relative px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-all duration-200">
                                <span class="relative z-10">Manage Items</span>
                                <div class="absolute inset-0 bg-green-500/10 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-200"></div>
                            </a>
                            <a href="<?php echo e(route('staff.borrowings.index')); ?>" class="group relative px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-all duration-200">
                                <span class="relative z-10">Requests</span>
                                <div class="absolute inset-0 bg-green-500/10 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-200"></div>
                            </a>
                        <?php endif; ?>
                        <?php if(auth()->user()->isAdmin()): ?>
                            <a href="<?php echo e(route('admin.users.index')); ?>" class="group relative px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-all duration-200">
                                <span class="relative z-10">Users</span>
                                <div class="absolute inset-0 bg-green-500/10 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-200"></div>
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="sm:hidden flex items-center">
                        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100/50 dark:hover:bg-gray-800/50 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                <path x-show="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- User Menu -->
                    <div class="hidden sm:flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="relative p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100/50 dark:hover:bg-gray-800/50 transition-colors group">
                            <svg class="w-6 h-6 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5V9a5 5 0 00-10 0v3l-5 5h5m0 0v1a3 3 0 006 0v-1m-3 0h3"></path>
                            </svg>
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
                        </button>
                        
                        <!-- Dark Mode Toggle -->
                        <button @click="darkMode = !darkMode" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100/50 dark:hover:bg-gray-800/50 transition-colors group">
                            <svg x-show="!darkMode" class="w-5 h-5 group-hover:text-yellow-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                            <svg x-show="darkMode" class="w-5 h-5 group-hover:text-yellow-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </button>

                        <!-- User Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center space-x-3 p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-white/50 dark:hover:bg-gray-800/50 transition-all duration-200 group">
                                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white text-sm font-semibold shadow-lg group-hover:shadow-xl transition-shadow">
                                    <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                                </div>
                                <div class="text-left hidden lg:block">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100"><?php echo e(auth()->user()->name); ?></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 capitalize"><?php echo e(auth()->user()->role); ?></p>
                                </div>
                                <svg class="w-4 h-4 transition-transform group-hover:rotate-180 duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-56 bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl border border-gray-200/20 dark:border-gray-700/30 py-2">
                                <div class="px-4 py-3 border-b border-gray-200/20 dark:border-gray-700/30">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100"><?php echo e(auth()->user()->name); ?></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 capitalize"><?php echo e(auth()->user()->email); ?></p>
                                </div>
                                <a href="#" class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100/50 dark:hover:bg-gray-700/50 transition-colors">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profile Settings
                                </a>
                                <a href="#" class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100/50 dark:hover:bg-gray-700/50 transition-colors">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Preferences
                                </a>
                                <div class="border-t border-gray-200/20 dark:border-gray-700/30 mt-2 pt-2">
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="flex items-center w-full px-4 py-3 text-sm text-red-600 dark:text-red-400 hover:bg-red-50/50 dark:hover:bg-red-900/20 transition-colors">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Sign out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Sidebar -->
            <div x-show="sidebarOpen" @click.away="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 sm:hidden">
                <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white/95 dark:bg-gray-900/95 backdrop-blur-xl border-r border-gray-200/20 dark:border-gray-700/30" x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
                    <div class="flex items-center justify-between px-4 py-4 border-b border-gray-200/20 dark:border-gray-700/30">
                        <div class="flex items-center space-x-2">
                            <img src="<?php echo e(asset('images/spup_logo.png')); ?>" alt="SPUP Logo" class="w-8 h-8 object-contain">
                            <h1 class="text-xl font-bold bg-gradient-to-r from-green-600 via-green-700 to-emerald-600 bg-clip-text text-transparent font-poppins">
                                INNOTRACK
                            </h1>
                        </div>
                        <button @click="sidebarOpen = false" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100/50 dark:hover:bg-gray-800/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="px-4 py-6 space-y-2">
                        <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center px-3 py-2 rounded-lg text-gray-900 dark:text-gray-100 hover:bg-green-500/10 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h2a2 2 0 012 2v2H8V5z"></path>
                            </svg>
                            Dashboard
                        </a>
                        <?php if(auth()->user()->isStaff() || auth()->user()->isAdmin()): ?>
                            <a href="<?php echo e(route('staff.items.index')); ?>" class="flex items-center px-3 py-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-green-500/10 transition-colors">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Manage Items
                            </a>
                            <a href="<?php echo e(route('staff.borrowings.index')); ?>" class="flex items-center px-3 py-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-green-500/10 transition-colors">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Requests
                            </a>
                        <?php endif; ?>
                        <?php if(auth()->user()->isAdmin()): ?>
                            <a href="<?php echo e(route('admin.users.index')); ?>" class="flex items-center px-3 py-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-green-500/10 transition-colors">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                Users
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <!-- User Profile in Mobile -->
                    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200/20 dark:border-gray-700/30">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100"><?php echo e(auth()->user()->name); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 capitalize"><?php echo e(auth()->user()->role); ?></p>
                            </div>
                        </div>
                        <div class="mt-3 flex space-x-2">
                            <button @click="darkMode = !darkMode" class="flex-1 py-2 px-3 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100/50 dark:hover:bg-gray-800/50 transition-colors text-sm">
                                <span x-show="!darkMode">🌙 Dark</span>
                                <span x-show="darkMode">☀️ Light</span>
                            </button>
                            <form method="POST" action="<?php echo e(route('logout')); ?>" class="flex-1">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="w-full py-2 px-3 rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50/50 dark:hover:bg-red-900/20 transition-colors text-sm">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </main>

        <!-- Success/Error Messages -->
        <?php if(session('success')): ?>
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed bottom-4 right-4 z-50">
                <div class="bg-green-500/90 backdrop-blur-md text-white px-6 py-3 rounded-xl shadow-lg border border-green-400/20">
                    <?php echo e(session('success')); ?>

                </div>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed bottom-4 right-4 z-50">
                <div class="bg-red-500/90 backdrop-blur-md text-white px-6 py-3 rounded-xl shadow-lg border border-red-400/20">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div><?php echo e($error); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html><?php /**PATH C:\Users\Cedric\innotrack\resources\views/layouts/app.blade.php ENDPATH**/ ?>