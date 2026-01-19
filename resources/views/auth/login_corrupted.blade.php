<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'INNOTRACK') }} - Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Fallback Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .slide-in { animation: slideIn 0.6s ease-out; }
        
        body {
            background-image: 
                radial-gradient(at 40% 20%, hsla(228, 67%, 73%, 1) 0px, transparent 50%),
                radial-gradient(at 80% 0%, hsla(189, 85%, 68%, 1) 0px, transparent 50%),
                radial-gradient(at 0% 50%, hsla(355, 85%, 93%, 1) 0px, transparent 50%),
                radial-gradient(at 80% 50%, hsla(340, 87%, 89%, 1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, hsla(22, 100%, 77%, 1) 0px, transparent 50%),
                radial-gradient(at 80% 100%, hsla(242, 100%, 70%, 1) 0px, transparent 50%),
                radial-gradient(at 0% 0%, hsla(343, 100%, 76%, 1) 0px, transparent 50%);
        }
        .glass {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
    </style>
</head>

<body class="font-inter min-h-screen">
    <div class="min-h-screen flex">
        <!-- Left Panel -->
        <div class="hidden lg:flex lg:w-1/2 bg-white/10 backdrop-blur-md relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute inset-0">
                <div class="absolute top-10 left-10 w-32 h-32 bg-white/20 rounded-full blur-xl"></div>
                <div class="absolute bottom-20 right-20 w-48 h-48 bg-blue-400/30 rounded-full blur-2xl"></div>
                <div class="absolute top-1/2 left-1/3 w-24 h-24 bg-purple-400/20 rounded-full blur-lg"></div>
            </div>
            
            <div class="relative z-10 flex flex-col justify-center px-12 py-24">
                <div class="slide-in">
                    <!-- Logo & Brand -->
                    <div class="mb-16">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white/30 rounded-2xl mb-8 backdrop-blur-sm">
                            <svg class="w-8 h-8 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h1a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h1 class="text-6xl font-bold mb-4 text-blue-900">INNOTRACK</h1>
                        <p class="text-2xl text-blue-700 font-medium mb-8">Laboratory Management System</p>
                        <p class="text-lg text-blue-800 leading-relaxed max-w-lg">
                            Streamline your laboratory operations with intelligent equipment tracking, seamless borrowing management, and comprehensive inventory control.
                        </p>
                    </div>
                    
                    <!-- Features Grid -->
                    <div class="grid grid-cols-2 gap-8 max-w-lg">
                        <div class="glass rounded-xl p-6 text-center">
                            <div class="w-12 h-12 bg-blue-500/20 rounded-lg mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-blue-900 mb-2">Fast & Efficient</h3>
                            <p class="text-sm text-blue-700">Quick access to all equipment</p>
                        </div>
                        
                        <div class="glass rounded-xl p-6 text-center">
                            <div class="w-12 h-12 bg-purple-500/20 rounded-lg mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-purple-900 mb-2">Secure Access</h3>
                            <p class="text-sm text-purple-700">Role-based permissions</p>
                        </div>
                        
                        <div class="glass rounded-xl p-6 text-center">
                            <div class="w-12 h-12 bg-green-500/20 rounded-lg mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-green-900 mb-2">Real-time Data</h3>
                            <p class="text-sm text-green-700">Live equipment tracking</p>
                        </div>
                        
                        <div class="glass rounded-xl p-6 text-center">
                            <div class="w-12 h-12 bg-orange-500/20 rounded-lg mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-orange-900 mb-2">Multi-user</h3>
                            <p class="text-sm text-orange-700">Students, staff & admins</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="flex-1 flex flex-col justify-center px-8 sm:px-12 lg:px-16 xl:px-24 bg-white dark:bg-gray-900">
            <div class="w-full max-w-md mx-auto slide-in" style="animation-delay: 0.2s;">
                <!-- Mobile Header -->
                <div class="lg:hidden text-center mb-12">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-600 rounded-xl mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h1a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">INNOTRACK</h1>
                    <p class="text-gray-600 dark:text-gray-400">Laboratory Management</p>
                </div>

                <!-- Welcome -->
                <div class="mb-10">
                    <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-3">Welcome back!</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400">Please enter your credentials to access your account</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-8 p-4 bg-green-50 border-l-4 border-green-400 text-green-700 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm">{{ session('status') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-8 p-4 bg-red-50 border-l-4 border-red-400 text-red-700 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium">There were some problems with your input:</h3>
                                <ul class="mt-2 text-sm list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-8">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">
                            Email Address
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </div>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autocomplete="username"
                                   placeholder="Enter your email address"
                                   class="w-full pl-10 pr-4 py-4 text-lg border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm focus:shadow-md">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">
                            Password
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="Enter your password"
                                   class="w-full pl-10 pr-4 py-4 text-lg border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm focus:shadow-md">
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center group cursor-pointer">
                            <input type="checkbox" 
                                   name="remember" 
                                   class="h-5 w-5 text-blue-600 border-2 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 transition-colors">
                            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Remember me for 30 days</span>
                        </label>
                        <a href="#" class="text-sm font-semibold text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-4 px-6 rounded-xl focus:ring-4 focus:ring-blue-500/50 focus:outline-none transition-all duration-200 transform hover:scale-[1.01] active:scale-[0.99] shadow-lg hover:shadow-xl text-lg">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Sign in to your dashboard
                        </span>
                    </button>
                </form>

                <!-- Support Section -->
                <div class="mt-10">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                        <div class="text-center">
                            <svg class="mx-auto h-8 w-8 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Need Help?</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                Contact your system administrator for account assistance or technical support.
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Available 24/7 • Response within 1 hour during business hours
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        &copy; {{ date('Y') }} INNOTRACK Laboratory Management System. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Dark Mode Toggle -->
    <button @click="darkMode = !darkMode" 
            class="fixed top-6 right-6 z-50 p-3 bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 border border-gray-200 dark:border-gray-700 group">
        <svg x-show="!darkMode" class="w-5 h-5 text-gray-600 group-hover:text-yellow-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
        </svg>
        <svg x-show="darkMode" class="w-5 h-5 text-gray-400 group-hover:text-yellow-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
    </button>
</body>
</html>
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
            transition: all 0.3s ease;
            z-index: 10;
        }
        .input-modern input:focus + .input-icon {
            color: rgba(59, 130, 246, 1);
            transform: translateY(-50%) scale(1.1);
        }
        
        .btn-modern {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            padding: 1.2rem 2rem;
            border-radius: 16px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            transform-style: preserve-3d;
        }
        .btn-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s;
        }
        .btn-modern:hover {
            transform: translateY(-3px) rotateX(5deg);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.4);
        }
        .btn-modern:hover::before {
            left: 100%;
        }
        
        .feature-modern {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        .feature-modern:hover {
            transform: translateY(-8px) scale(1.05);
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }
        .floating-element {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 8s ease-in-out infinite;
            filter: blur(20px);
        }
        .floating-element:nth-child(1) {
            top: 10%;
            left: 15%;
            width: 120px;
            height: 120px;
            animation-delay: 0s;
        }
        .floating-element:nth-child(2) {
            top: 20%;
            right: 20%;
            width: 80px;
            height: 80px;
            animation-delay: -2s;
            background: rgba(59, 130, 246, 0.2);
        }
        .floating-element:nth-child(3) {
            bottom: 30%;
            left: 10%;
            width: 150px;
            height: 150px;
            animation-delay: -4s;
            background: rgba(147, 51, 234, 0.15);
        }
        .floating-element:nth-child(4) {
            bottom: 15%;
            right: 15%;
            width: 90px;
            height: 90px;
            animation-delay: -1s;
            background: rgba(236, 72, 153, 0.2);
        }
    </style>
</head>

<body class="font-inter antialiased overflow-hidden">
    <!-- Animated Background -->
    <div class="gradient-bg fixed inset-0 z-0"></div>
    
    <!-- Floating Elements -->
    <div class="floating-elements z-10">
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-20 min-h-screen flex">
        <!-- Left Side - Branding & Features -->
        <div class="hidden lg:flex lg:flex-1 flex-col justify-center px-12 text-white slide-in-left">
            <div class="max-w-lg">
                <!-- Logo & Title -->
                <div class="mb-12 fade-in">
                    <div class="flex items-center mb-8">
                        <div class="w-16 h-16 bg-gradient-to-br from-white/20 to-white/10 rounded-2xl flex items-center justify-center mr-6 shadow-xl">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h1a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-5xl font-bold font-poppins tracking-wide mb-2">INNOTRACK</h1>
                            <p class="text-xl text-white/80">Laboratory Management</p>
                        </div>
                    </div>
                    <p class="text-white/90 text-lg leading-relaxed">
                        Transform your laboratory operations with our intelligent equipment tracking and borrowing management platform.
                    </p>
                </div>

                <!-- Features -->
                <div class="space-y-6">
                    <div class="feature-modern" style="animation-delay: 0.2s">
                        <div class="w-16 h-16 bg-blue-500/20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Smart Equipment Tracking</h3>
                        <p class="text-white/70">Real-time monitoring of all laboratory equipment with advanced analytics</p>
                    </div>

                    <div class="feature-modern" style="animation-delay: 0.4s">
                        <div class="w-16 h-16 bg-purple-500/20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Role-Based Access</h3>
                        <p class="text-white/70">Secure authentication system for students, staff, and administrators</p>
                    </div>

                    <div class="feature-modern" style="animation-delay: 0.6s">
                        <div class="w-16 h-16 bg-pink-500/20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-pink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Lightning Fast</h3>
                        <p class="text-white/70">Instant access to equipment status and borrowing history</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dark Mode Toggle -->
    <button @click="darkMode = !darkMode" 
            class="fixed top-6 right-6 z-50 p-3 bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 border border-gray-200 dark:border-gray-700 group">
        <svg x-show="!darkMode" class="w-5 h-5 text-gray-600 group-hover:text-yellow-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
        </svg>
        <svg x-show="darkMode" class="w-5 h-5 text-gray-400 group-hover:text-yellow-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
    </button>
</body>
</html>