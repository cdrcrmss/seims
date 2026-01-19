<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In - {{ config('app.name', 'INNOTRACK') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&family=space-grotesk:400,500,600,700&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --secondary: #06b6d4;
            --accent: #f59e0b;
            --success: #10b981;
            --error: #ef4444;
            --warning: #f97316;
            --surface: rgba(255, 255, 255, 0.08);
            --surface-hover: rgba(255, 255, 255, 0.12);
            --glass-border: rgba(255, 255, 255, 0.15);
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.8);
            --text-muted: rgba(255, 255, 255, 0.6);
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, 
                #667eea 0%, 
                #764ba2 15%, 
                #f093fb 35%, 
                #4facfe 55%, 
                #00f2fe 75%, 
                #43e97b 95%);
            background-size: 400% 400%;
            animation: gradientFlow 15s ease infinite;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
            position: relative;
        }
        
        /* Enhanced background animation */
        @keyframes gradientFlow {
            0% { background-position: 0% 50%; }
            25% { background-position: 100% 50%; }
            50% { background-position: 50% 100%; }
            75% { background-position: 0% 50%; }
            100% { background-position: 50% 0%; }
        }
        
        /* Smooth animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-15px) rotate(2deg); }
            66% { transform: translateY(-8px) rotate(-2deg); }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.05); opacity: 1; }
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 20px rgba(99, 102, 241, 0.3); }
            50% { box-shadow: 0 0 40px rgba(99, 102, 241, 0.6); }
        }
        
        /* Animation classes */
        .animate-slide-up { 
            animation: slideInUp 0.8s cubic-bezier(0.165, 0.84, 0.44, 1); 
        }
        .animate-slide-left { 
            animation: slideInLeft 0.8s cubic-bezier(0.165, 0.84, 0.44, 1); 
        }
        .animate-fade-in { 
            animation: fadeIn 1.2s ease-out; 
        }
        .animate-float { 
            animation: float 6s ease-in-out infinite; 
        }
        .animate-pulse-slow { 
            animation: pulse 3s ease-in-out infinite; 
        }
        .animate-glow {
            animation: glow 2s ease-in-out infinite;
        }
        
        /* Enhanced glass effect */
        .glass {
            background: var(--surface);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
        
        .glass-strong {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.15),
                0 8px 32px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.25),
                inset 0 -1px 0 rgba(255, 255, 255, 0.1);
        }
        
        /* Enhanced input styling */
        .input-field {
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid rgba(255, 255, 255, 0.15);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }
        
        .input-field:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: var(--primary);
            box-shadow: 
                0 0 0 4px rgba(99, 102, 241, 0.15),
                0 8px 25px rgba(99, 102, 241, 0.1);
            outline: none;
            transform: translateY(-2px);
        }
        
        .input-field::placeholder {
            color: var(--text-muted);
        }
        
        /* Enhanced button styling */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 50%, var(--secondary) 100%);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateY(0);
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
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-primary:hover {
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.6);
            transform: translateY(-3px);
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:active {
            transform: translateY(-1px);
        }
        
        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: translateY(0);
        }
        
        /* Feature cards */
        .feature-card {
            background: var(--surface);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        }
        
        .feature-card:hover {
            background: var(--surface-hover);
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }
        
        /* Floating elements */
        .floating-element {
            position: absolute;
            border-radius: 50%;
            opacity: 0.06;
            animation: float 8s ease-in-out infinite;
            filter: blur(1px);
        }
        
        .floating-element:nth-child(1) {
            top: 15%;
            left: 8%;
            width: 120px;
            height: 120px;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            animation-delay: 0s;
        }
        
        .floating-element:nth-child(2) {
            top: 60%;
            right: 15%;
            width: 160px;
            height: 160px;
            background: linear-gradient(45deg, #4ecdc4, #45b7d1);
            animation-delay: -2s;
        }
        
        .floating-element:nth-child(3) {
            bottom: 25%;
            left: 15%;
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #96ceb4, #ffeaa7);
            animation-delay: -4s;
        }
        
        .floating-element:nth-child(4) {
            top: 30%;
            right: 35%;
            width: 100px;
            height: 100px;
            background: linear-gradient(45deg, #a8edea, #fed6e3);
            animation-delay: -6s;
        }
        
        /* Logo styling */
        .logo-container {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
        }
        
        /* Alert styling */
        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        .success-message {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        /* Checkbox styling */
        .custom-checkbox {
            appearance: none;
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .custom-checkbox:checked {
            background: var(--primary);
            border-color: var(--primary);
        }
        
        .custom-checkbox:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        
        /* Mobile optimizations */
        @media (max-width: 768px) {
            body {
                overflow-y: auto;
            }
            
            .feature-card {
                margin-bottom: 1rem;
            }
            
            .glass-strong {
                margin: 1rem;
            }
        }
        
        /* Loading spinner */
        .spinner {
            width: 24px;
            height: 24px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Accessibility improvements */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        
        /* Focus styles for keyboard navigation */
        .focus-visible:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
    </style>
</head>

<body class="min-h-screen relative">
    <!-- Enhanced floating background elements -->
    <div class="fixed inset-0 pointer-events-none">
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
    </div>
    
    <div class="min-h-screen flex relative">
        <!-- Left Panel - Brand & Features -->
        <div class="hidden lg:flex lg:w-1/2 relative p-8 xl:p-12 overflow-hidden">
            <div class="w-full flex flex-col justify-center relative z-10 max-w-2xl mx-auto">
                <!-- Logo & Title -->
                <div class="animate-slide-left">
                    <div class="flex items-center space-x-5 mb-12">
                        <div class="w-18 h-18 logo-container rounded-3xl flex items-center justify-center animate-float">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 7V17C3 18.1 3.9 19 5 19H19C20.1 19 21 18.1 21 17V7C21 5.9 20.1 5 19 5H5C3.9 5 3 5.9 3 7Z" fill="white" fill-opacity="0.3"/>
                                <path d="M8 15H7V9H8V15ZM12 15H11V7H12V15ZM16 15H15V11H16V15Z" fill="white"/>
                                <path d="M12 2L22 6V8L12 4L2 8V6L12 2Z" fill="white" fill-opacity="0.7"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-5xl xl:text-6xl font-bold text-white mb-2 font-space-grotesk">INNOTRACK</h1>
                            <p class="text-xl xl:text-2xl text-white/80 font-medium">Laboratory Management Excellence</p>
                        </div>
                    </div>
                </div>
                
                <!-- Main Content -->
                <div class="animate-slide-left" style="animation-delay: 0.2s;">
                    <h2 class="text-5xl xl:text-7xl font-bold text-white mb-8 leading-tight font-space-grotesk">
                        The Future of
                        <span class="block bg-gradient-to-r from-yellow-200 via-pink-200 to-purple-200 bg-clip-text text-transparent">
                            Scientific Innovation
                        </span>
                    </h2>
                    <p class="text-xl xl:text-2xl text-white/90 mb-16 leading-relaxed font-medium">
                        Streamline your laboratory operations with cutting-edge technology, intelligent automation, and seamless collaboration tools.
                    </p>
                </div>
                
                <!-- Features Grid -->
                <div class="grid grid-cols-1 gap-8">
                    <div class="feature-card rounded-3xl p-8 animate-slide-left" style="animation-delay: 0.4s;">
                        <div class="flex items-start space-x-6">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-purple-500 rounded-3xl flex items-center justify-center flex-shrink-0 animate-glow">
                                <svg width="28" height="28" fill="none" stroke="white" strokeWidth="2.5" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-2xl xl:text-3xl font-bold text-white mb-3 font-space-grotesk">Lightning Performance</h3>
                                <p class="text-white/80 text-lg xl:text-xl leading-relaxed">Real-time tracking with millisecond response times and instant synchronization across all devices.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="feature-card rounded-3xl p-8 animate-slide-left" style="animation-delay: 0.6s;">
                        <div class="flex items-start space-x-6">
                            <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-blue-500 rounded-3xl flex items-center justify-center flex-shrink-0 animate-glow" style="animation-delay: 1s;">
                                <svg width="28" height="28" fill="none" stroke="white" strokeWidth="2.5" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-2xl xl:text-3xl font-bold text-white mb-3 font-space-grotesk">Enterprise Security</h3>
                                <p class="text-white/80 text-lg xl:text-xl leading-relaxed">Military-grade encryption with advanced access controls and comprehensive audit trails.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="feature-card rounded-3xl p-8 animate-slide-left" style="animation-delay: 0.8s;">
                        <div class="flex items-start space-x-6">
                            <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-pink-500 rounded-3xl flex items-center justify-center flex-shrink-0 animate-glow" style="animation-delay: 2s;">
                                <svg width="28" height="28" fill="none" stroke="white" strokeWidth="2.5" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-2xl xl:text-3xl font-bold text-white mb-3 font-space-grotesk">AI-Powered Insights</h3>
                                <p class="text-white/80 text-lg xl:text-xl leading-relaxed">Machine learning algorithms provide predictive analytics and intelligent resource optimization.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Panel - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-4 sm:p-6 lg:p-12 relative">
            <!-- Mobile Background -->
            <div class="lg:hidden absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
            
            <div class="w-full max-w-lg relative z-10">
                <div class="glass-strong rounded-3xl p-6 sm:p-8 lg:p-12 animate-slide-up">
                    <!-- Mobile Logo -->
                    <div class="lg:hidden flex flex-col items-center mb-8">
                        <div class="w-24 h-24 logo-container rounded-3xl flex items-center justify-center animate-float mb-4">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                                <path d="M3 7V17C3 18.1 3.9 19 5 19H19C20.1 19 21 18.1 21 17V7C21 5.9 20.1 5 19 5H5C3.9 5 3 5.9 3 7Z" fill="white" fill-opacity="0.3"/>
                                <path d="M8 15H7V9H8V15ZM12 15H11V7H12V15ZM16 15H15V11H16V15Z" fill="white"/>
                                <path d="M12 2L22 6V8L12 4L2 8V6L12 2Z" fill="white" fill-opacity="0.7"/>
                            </svg>
                        </div>
                        <h1 class="text-4xl font-bold bg-gradient-to-r from-white via-blue-100 to-purple-100 bg-clip-text text-transparent font-space-grotesk">INNOTRACK</h1>
                        <p class="text-lg text-white/80 mt-2">Laboratory Excellence</p>
                    </div>
                    
                    <!-- Form Header -->
                    <div class="text-center mb-8">
                        <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-indigo-500 via-purple-500 to-cyan-500 rounded-3xl flex items-center justify-center animate-pulse-slow shadow-2xl">
                            <svg width="36" height="36" fill="white" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-bold text-white mb-3 font-space-grotesk">Welcome Back</h2>
                        <p class="text-lg sm:text-xl text-white/80 font-medium">Access your laboratory dashboard</p>
                    </div>
                    
                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="success-message rounded-xl p-4 mb-6 animate-fade-in">
                            <div class="flex items-center space-x-3">
                                <svg class="w-6 h-6 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-green-300 font-medium">{{ session('status') }}</p>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-6" x-data="{ 
                        showPassword: false, 
                        loading: false,
                        email: '{{ old('email') }}',
                        password: '',
                        emailFocused: false,
                        passwordFocused: false
                    }">
                        @csrf
                        
                        <!-- Email Field -->
                        <div class="space-y-3">
                            <label for="email" class="block text-lg font-semibold text-white/90">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors"
                                     :class="emailFocused ? 'text-indigo-400' : 'text-white/60'">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </div>
                                <input id="email" 
                                       type="email" 
                                       name="email" 
                                       x-model="email"
                                       @focus="emailFocused = true"
                                       @blur="emailFocused = false"
                                       required 
                                       autofocus 
                                       autocomplete="username"
                                       placeholder="Enter your email address"
                                       class="w-full pl-14 pr-4 py-5 input-field rounded-2xl text-white text-lg font-medium placeholder:text-white/50 focus-visible">
                                @error('email')
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @enderror
                            </div>
                            @error('email')
                                <div class="error-message rounded-lg p-3 animate-fade-in">
                                    <p class="text-red-300 text-sm font-medium flex items-center">
                                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                </div>
                            @enderror
                        </div>
                        
                        <!-- Password Field -->
                        <div class="space-y-3">
                            <label for="password" class="block text-lg font-semibold text-white/90">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors"
                                     :class="passwordFocused ? 'text-indigo-400' : 'text-white/60'">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input id="password" 
                                       :type="showPassword ? 'text' : 'password'" 
                                       name="password" 
                                       x-model="password"
                                       @focus="passwordFocused = true"
                                       @blur="passwordFocused = false"
                                       required 
                                       autocomplete="current-password"
                                       placeholder="Enter your password"
                                       class="w-full pl-14 pr-14 py-5 input-field rounded-2xl text-white text-lg font-medium placeholder:text-white/50 focus-visible">
                                <button type="button" 
                                        @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-white/60 hover:text-white transition-colors duration-300 focus-visible"
                                        tabindex="-1">
                                    <svg x-show="!showPassword" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPassword" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <div class="error-message rounded-lg p-3 animate-fade-in">
                                    <p class="text-red-300 text-sm font-medium flex items-center">
                                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                </div>
                            @enderror
                        </div>
                        
                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between pt-2">
                            <label class="flex items-center group cursor-pointer">
                                <input type="checkbox" 
                                       name="remember" 
                                       class="custom-checkbox mr-3 focus-visible">
                                <span class="text-lg text-white/80 group-hover:text-white transition-colors select-none">Remember me</span>
                            </label>
                            
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" 
                                   class="text-lg text-cyan-300 hover:text-cyan-200 transition-colors font-medium focus-visible">
                                    Forgot Password?
                                </a>
                            @endif
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" 
                                @click="loading = true"
                                :disabled="loading"
                                class="w-full btn-primary text-white font-bold py-5 px-8 rounded-2xl text-xl relative overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed focus-visible mt-8">
                            <span x-show="!loading" class="flex items-center justify-center space-x-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                <span>Access Dashboard</span>
                            </span>
                            <span x-show="loading" class="flex items-center justify-center space-x-3" x-cloak>
                                <div class="spinner"></div>
                                <span>Signing In...</span>
                            </span>
                        </button>
                    </form>
                    
                    <!-- Support Section -->
                    <div class="mt-10 text-center">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-white/20"></div>
                            </div>
                            <div class="relative flex justify-center text-lg">
                                <span class="px-6 bg-transparent text-white/60 font-medium">Need assistance?</span>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="button" class="glass rounded-xl px-6 py-3 text-white/80 hover:text-white hover:bg-white/10 transition-all duration-300 flex items-center justify-center space-x-2 mx-auto group focus-visible">
                                <svg class="w-5 h-5 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Contact Support Team</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>