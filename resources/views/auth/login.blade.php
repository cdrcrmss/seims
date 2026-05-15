<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In - {{ config('app.name', 'SEIMS') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/spup_logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/spup_logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/spup_logo.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    
    <style>
        * {
            box-sizing: border-box;
        }
        
        [x-cloak] { display: none !important; }

        /* Hide native browser password reveal/clear buttons */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear { display: none !important; }
        input[type=password]::-webkit-credentials-auto-fill-button,
        input[type=password]::-webkit-strong-password-auto-fill-button { display: none !important; visibility: hidden; pointer-events: none; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            min-height: 100vh;
            background: #f8fafc;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }

        .animate-slide-in {
            animation: slideIn 0.6s ease-out forwards;
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }

        /* Brand Panel Gradient */
        .brand-panel {
            background: linear-gradient(135deg, #166534 0%, #14532d 100%);
            position: relative;
            overflow: hidden;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(22, 163, 74, 0.1) 0%, transparent 50%);
            animation: rotate 30s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Decorative Elements */
        .circle-decoration {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
        }

        .circle-1 {
            width: 300px;
            height: 300px;
            background: #16a34a;
            top: -100px;
            right: -100px;
        }

        .circle-2 {
            width: 200px;
            height: 200px;
            background: #22c55e;
            bottom: 10%;
            left: -50px;
        }

        .circle-3 {
            width: 150px;
            height: 150px;
            background: #15803d;
            top: 40%;
            right: 10%;
        }

        /* Input Styling */
        .form-input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #ffffff;
            color: #1e293b;
        }

        .form-input:focus {
            outline: none;
            border-color: #16a34a;
            box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.1);
        }

        .form-input::placeholder {
            color: #94a3b8;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: color 0.3s ease;
        }

        .input-wrapper:focus-within .input-icon {
            color: #16a34a;
        }

        /* Button Styling */
        .btn-primary {
            width: 100%;
            padding: 16px 24px;
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(22, 163, 74, 0.4);
            position: relative;
            overflow: hidden;
            text-align: center;
        }
        
        .btn-primary .btn-content {
            display: inline-flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(22, 163, 74, 0.5);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Checkbox Styling */
        .custom-checkbox {
            appearance: none;
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #cbd5e1;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            background: white;
        }

        .custom-checkbox:checked {
            background: #16a34a;
            border-color: #16a34a;
        }

        .custom-checkbox:checked::after {
            content: '';
            position: absolute;
            left: 6px;
            top: 2px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        /* Alert Styling */
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #16a34a;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
        }

        /* Feature Cards */
        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 16px 0;
        }

        .feature-icon {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Spinner */
        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .brand-panel {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div style="display: flex; min-height: 100vh;">
        <!-- Left Brand Panel -->
        <div class="brand-panel" style="flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 48px; position: relative;">
            <!-- Decorative circles -->
            <div class="circle-decoration circle-1"></div>
            <div class="circle-decoration circle-2"></div>
            <div class="circle-decoration circle-3"></div>
            
            <div style="position: relative; z-index: 10; max-width: 480px;">
                <!-- Logo -->
                <div class="animate-slide-in" style="display: flex; align-items: center; gap: 16px; margin-bottom: 48px;">
                    <div class="animate-float" style="width: 56px; height: 56px; background: linear-gradient(135deg, #16a34a, #22c55e); border-radius: 16px; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ asset('images/spup_logo.png') }}" alt="SPUP Logo" style="width: 48px; height: 48px; object-fit: contain;">
                    </div>
                    <div>
                        <h1 style="font-size: 28px; font-weight: 700; color: white; margin: 0; letter-spacing: -0.5px;">SEIMS</h1>
                        <p style="font-size: 14px; color: rgba(255,255,255,0.6); margin: 4px 0 0 0;">Laboratory Management System</p>
                    </div>
                </div>

                <!-- Headline -->
                <div class="animate-fade-in-up delay-100">
                    <h2 style="font-size: 40px; font-weight: 700; color: white; line-height: 1.2; margin: 0 0 24px 0;">
                        Streamline Your<br>
                        <span style="background: linear-gradient(135deg, #86efac, #4ade80); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Laboratory Operations</span>
                    </h2>
                    <p style="font-size: 18px; color: rgba(255,255,255,0.7); line-height: 1.7; margin: 0 0 48px 0;">
                        Efficiently manage equipment, track resources, and optimize workflows with our comprehensive laboratory management platform.
                    </p>
                </div>

            </div>

            <!-- Footer -->
            <div class="animate-fade-in delay-400" style="position: absolute; bottom: 48px; left: 48px; right: 48px;">
                <p style="font-size: 14px; color: rgba(255,255,255,0.4); margin: 0;">
                    © {{ date('Y') }} SEIMS. All rights reserved.
                </p>
            </div>
        </div>

        <!-- Right Login Form Panel -->
        <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 48px; background: #f8fafc;">
            <div style="width: 100%; max-width: 420px;" class="animate-fade-in-up">
                <!-- Mobile Logo -->
                <div style="display: none; text-align: center; margin-bottom: 40px;" class="mobile-logo">
                    <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #16a34a, #22c55e); border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                        <img src="{{ asset('images/spup_logo.png') }}" alt="SPUP Logo" style="width: 56px; height: 56px; object-fit: contain;">
                    </div>
                    <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0;">SEIMS</h1>
                </div>

                <style>
                    @media (max-width: 1024px) {
                        .mobile-logo { display: block !important; }
                    }
                </style>

                <!-- Form Header -->
                <div style="margin-bottom: 32px;">
                    <h2 style="font-size: 28px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">Welcome back</h2>
                    <p style="font-size: 16px; color: #64748b; margin: 0;">Please enter your credentials to continue</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert-success animate-fade-in" style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" x-data="{ 
                    showPassword: false, 
                    loading: false,
                    email: '{{ old('email') }}',
                    password: ''
                }" @submit="loading = true">
                    @csrf

                    <!-- Email Field -->
                    <div style="margin-bottom: 20px;">
                        <label for="email" style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">
                            Email Address
                        </label>
                        <div class="input-wrapper" style="position: relative;">
                            <svg class="input-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                x-model="email"
                                required 
                                autofocus 
                                autocomplete="username"
                                placeholder="name@company.com"
                                class="form-input"
                            >
                        </div>
                        @error('email')
                            <div class="alert-error animate-fade-in" style="margin-top: 8px; display: flex; align-items: center; gap: 8px;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>
                    
                    <!-- Password Field -->
                    <div style="margin-bottom: 20px;">
                        <label for="password" style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">
                            Password
                        </label>
                        <div class="input-wrapper" style="position: relative;">
                            <svg class="input-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <input 
                                id="password" 
                                :type="showPassword ? 'text' : 'password'" 
                                name="password" 
                                x-model="password"
                                required 
                                autocomplete="current-password"
                                placeholder="Enter your password"
                                class="form-input"
                                style="padding-right: 48px;"
                            >
                            <button 
                                type="button" 
                                @click="showPassword = !showPassword"
                                style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #94a3b8; transition: color 0.2s;"
                                onmouseover="this.style.color='#64748b'" 
                                onmouseout="this.style.color='#94a3b8'"
                            >
                                <svg x-show="!showPassword" x-cloak width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showPassword" x-cloak width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <div class="alert-error animate-fade-in" style="margin-top: 8px; display: flex; align-items: center; gap: 8px;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>
                    
                    <!-- Remember Me & Forgot Password -->
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="remember" class="custom-checkbox">
                            <span style="font-size: 14px; color: #64748b;">Remember me</span>
                        </label>
                        
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" style="font-size: 14px; color: #16a34a; text-decoration: none; font-weight: 500; transition: color 0.2s;" onmouseover="this.style.color='#15803d'" onmouseout="this.style.color='#16a34a'">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    
                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="btn-primary"
                        :disabled="loading"
                        style="min-height: 56px;"
                    >
                        <template x-if="!loading">
                            <span class="btn-content">
                                Sign in
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink: 0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </span>
                        </template>
                        <template x-if="loading">
                            <span class="btn-content">
                                <div class="spinner"></div>
                                Signing in...
                            </span>
                        </template>
                    </button>
                </form>

                <!-- Divider -->
                <div style="display: flex; align-items: center; gap: 16px; margin: 32px 0;">
                    <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
                    <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">or</span>
                    <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
                </div>

                <!-- Create Account Link -->
                <div style="text-align: center; margin-bottom: 24px;">
                    <p style="font-size: 14px; color: #64748b; margin: 0;">
                        Don't have an account?
                        <a href="{{ route('register') }}" style="color: #16a34a; text-decoration: none; font-weight: 600; transition: color 0.2s;" onmouseover="this.style.color='#15803d'" onmouseout="this.style.color='#16a34a'">
                            Create Account
                        </a>
                    </p>
                </div>

                <!-- Divider -->
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                    <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
                    <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Need help?</span>
                    <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
                </div>

                <!-- Support Link -->
                <div style="text-align: center;">
                    <a href="#" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border: 1px solid #e2e8f0; border-radius: 10px; text-decoration: none; color: #64748b; font-size: 14px; font-weight: 500; transition: all 0.2s;" onmouseover="this.style.borderColor='#cbd5e1'; this.style.background='#f8fafc'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.background='transparent'">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
