<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'INNOTRACK') }} - Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Fallback Tailwind CSS CDN for immediate testing -->
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
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
            50% { box-shadow: 0 0 30px rgba(59, 130, 246, 0.5), 0 0 40px rgba(147, 51, 234, 0.3); }
        }
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        @keyframes slide-in-left {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes slide-in-right {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .float-animation { animation: float 6s ease-in-out infinite; }
        .gradient-bg { 
            background: linear-gradient(-45deg, #667eea, #764ba2, #667eea, #f093fb);
            background-size: 400% 400%;
            animation: gradient-shift 15s ease infinite;
        }
        .slide-in-left { animation: slide-in-left 0.8s ease-out; }
        .slide-in-right { animation: slide-in-right 0.8s ease-out; }
        .glass-card {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.1);
        }
        .input-group {
            position: relative;
            margin: 1.5rem 0;
        }
        .input-group input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            font-size: 1rem;
            color: white;
            outline: none;
            transition: all 0.3s ease;
        }
        .input-group input:focus {
            border-color: rgba(59, 130, 246, 0.8);
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2);
        }
        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
            transition: color 0.3s ease;
        }
        .input-group input:focus + .input-icon {
            color: rgba(59, 130, 246, 0.9);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            padding: 1rem 2rem;
            border-radius: 15px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .btn-primary:hover::before {
            left: 100%;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }
        .feature-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
    </style>
        .glow-pulse { animation: pulse-glow 4s ease-in-out infinite; }
        .gradient-border {
            background: linear-gradient(45deg, rgba(59, 130, 246, 0.3), rgba(147, 51, 234, 0.3), rgba(236, 72, 153, 0.3));
            background-size: 200% 200%;
            animation: gradient-shift 8s ease infinite;
        }
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
    </style>
</head>
<body class="font-inter antialiased overflow-hidden">
    <!-- Animated Background -->
    <div class="fixed inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-purple-900"></div>
        <div class="absolute top-10 left-10 w-72 h-72 bg-blue-400/20 dark:bg-blue-600/20 rounded-full filter blur-xl opacity-70 float-animation"></div>
        <div class="absolute top-40 right-10 w-96 h-96 bg-purple-400/20 dark:bg-purple-600/20 rounded-full filter blur-xl opacity-70 float-animation" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-10 left-1/2 w-80 h-80 bg-pink-400/20 dark:bg-pink-600/20 rounded-full filter blur-xl opacity-70 float-animation" style="animation-delay: 4s;"></div>
    </div>
    
    <div class="relative z-10 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 animate-fade-in">
            <!-- Logo -->
            <div class="text-center transform hover:scale-105 transition-all duration-300">
                <div class="mb-4 glow-pulse">
                    <h1 class="text-5xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent mb-2 font-poppins">
                        INNOTRACK
                    </h1>
                </div>
                <p class="text-gray-600 dark:text-gray-300 text-lg font-medium">Laboratory Management System</p>
                <div class="mt-4 w-16 h-1 bg-gradient-to-r from-blue-500 to-purple-600 mx-auto rounded-full"></div>
            </div>

            <!-- Login Form -->
            <div class="bg-white/10 dark:bg-white/5 backdrop-blur-xl rounded-3xl shadow-2xl border gradient-border p-8 transform hover:scale-[1.02] transition-all duration-300">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Welcome Back!</h2>
                    <p class="text-gray-600 dark:text-gray-400">Sign in to access your dashboard</p>
                </div>
                
                <form method="POST" action="{{ route('login') }}" class="space-y-6" x-data="{ emailFocused: false, passwordFocused: false }">
                    @csrf

                    <!-- Email -->
                    <div class="relative">
                        <label for="email" class="absolute left-3 text-sm font-medium text-gray-500 dark:text-gray-400 transition-all duration-200"
                               :class="emailFocused || document.getElementById('email').value ? 'top-1 text-xs text-blue-600 dark:text-blue-400' : 'top-3.5'">
                            Email address
                        </label>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                               value="{{ old('email') }}"
                               @focus="emailFocused = true" @blur="emailFocused = false"
                               class="w-full px-3 pt-6 pb-3 border border-gray-300/50 dark:border-gray-600/50 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 bg-white/30 dark:bg-gray-800/30 backdrop-blur-sm text-gray-900 dark:text-white placeholder-transparent transition-all duration-300 hover:bg-white/40 dark:hover:bg-gray-800/40">
                        @error('email')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"></path></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="relative" x-data="{ showPassword: false }">
                        <label for="password" class="absolute left-3 text-sm font-medium text-gray-500 dark:text-gray-400 transition-all duration-200"
                               :class="passwordFocused || document.getElementById('password').value ? 'top-1 text-xs text-blue-600 dark:text-blue-400' : 'top-3.5'">
                            Password
                        </label>
                        <input id="password" name="password" autocomplete="current-password" required
                               :type="showPassword ? 'text' : 'password'"
                               @focus="passwordFocused = true" @blur="passwordFocused = false"
                               class="w-full px-3 pt-6 pb-3 pr-12 border border-gray-300/50 dark:border-gray-600/50 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 bg-white/30 dark:bg-gray-800/30 backdrop-blur-sm text-gray-900 dark:text-white placeholder-transparent transition-all duration-300 hover:bg-white/40 dark:hover:bg-gray-800/40">
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                            </svg>
                        </button>
                        @error('password')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"></path></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded bg-white/50 dark:bg-gray-700/50">
                            <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Remember me</label>
                        </div>
                        <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-base font-semibold rounded-xl text-white bg-gradient-to-r from-blue-500 via-purple-600 to-pink-600 hover:from-blue-600 hover:via-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-2xl">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-white/60 group-hover:text-white/80 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                        </span>
                        Sign in to INNOTRACK
                    </button>
                </form>

                <!-- Demo Accounts -->
                <div class="mt-6 pt-6 border-t border-gray-200/20 dark:border-gray-700/30">
                    <p class="text-xs text-gray-600 dark:text-gray-400 text-center mb-3">Demo Accounts (password: password123)</p>
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <div class="text-center p-2 bg-blue-500/10 rounded-lg">
                            <p class="font-medium text-blue-600 dark:text-blue-400">Admin</p>
                            <p class="text-gray-600 dark:text-gray-400">admin@innotrack.com</p>
                        </div>
                        <div class="text-center p-2 bg-green-500/10 rounded-lg">
                            <p class="font-medium text-green-600 dark:text-green-400">Staff</p>
                            <p class="text-gray-600 dark:text-gray-400">staff@innotrack.com</p>
                        </div>
                        <div class="text-center p-2 bg-purple-500/10 rounded-lg">
                            <p class="font-medium text-purple-600 dark:text-purple-400">Student</p>
                            <p class="text-gray-600 dark:text-gray-400">student@innotrack.com</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dark Mode Toggle -->
            <div class="text-center">
                <button @click="darkMode = !darkMode" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-white/10 dark:hover:bg-white/5 backdrop-blur-md transition-colors">
                    <svg x-show="!darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                    <svg x-show="darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</body>
</html>