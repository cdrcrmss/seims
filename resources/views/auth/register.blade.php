<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account - {{ config('app.name', 'SEIMS') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    
    <style>
        * { box-sizing: border-box; }
        [x-cloak] { display: none !important; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            min-height: 100vh;
            background: #f8fafc;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
        .animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }
        .animate-slide-in { animation: slideIn 0.6s ease-out forwards; }
        .animate-float { animation: float 3s ease-in-out infinite; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }

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

        .circle-decoration { position: absolute; border-radius: 50%; opacity: 0.1; }
        .circle-1 { width: 300px; height: 300px; background: #16a34a; top: -100px; right: -100px; }
        .circle-2 { width: 200px; height: 200px; background: #22c55e; bottom: 10%; left: -50px; }
        .circle-3 { width: 150px; height: 150px; background: #15803d; top: 40%; right: 10%; }

        .form-input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #ffffff;
            color: #1e293b;
        }
        .form-input:focus {
            outline: none;
            border-color: #16a34a;
            box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.1);
        }
        .form-input::placeholder { color: #94a3b8; }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: color 0.3s ease;
        }
        .input-wrapper:focus-within .input-icon { color: #16a34a; }

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
            min-height: 56px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(22, 163, 74, 0.5);
        }
        .btn-primary:active { transform: translateY(0); }
        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        .btn-primary .btn-content {
            display: inline-flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
        }

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

        @media (max-width: 1024px) {
            .brand-panel { display: none; }
            .mobile-logo { display: block !important; }
        }
    </style>
</head>

<body>
    <div style="display: flex; min-height: 100vh;">
        <!-- Left Brand Panel -->
        <div class="brand-panel" style="flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 48px; position: relative;">
            <div class="circle-decoration circle-1"></div>
            <div class="circle-decoration circle-2"></div>
            <div class="circle-decoration circle-3"></div>
            
            <div style="position: relative; z-index: 10; max-width: 480px;">
                <div class="animate-slide-in" style="display: flex; align-items: center; gap: 16px; margin-bottom: 48px;">
                    <div class="animate-float" style="width: 56px; height: 56px; background: linear-gradient(135deg, #16a34a, #22c55e); border-radius: 16px; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ asset('images/spup_logo.png') }}" alt="SPUP Logo" style="width: 48px; height: 48px; object-fit: contain;">
                    </div>
                    <div>
                        <h1 style="font-size: 28px; font-weight: 700; color: white; margin: 0; letter-spacing: -0.5px;">SEIMS</h1>
                        <p style="font-size: 14px; color: rgba(255,255,255,0.6); margin: 4px 0 0 0;">Laboratory Management System</p>
                    </div>
                </div>

                <div class="animate-fade-in-up delay-100">
                    <h2 style="font-size: 40px; font-weight: 700; color: white; line-height: 1.2; margin: 0 0 24px 0;">
                        Join the<br>
                        <span style="background: linear-gradient(135deg, #86efac, #4ade80); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">SEIMS Platform</span>
                    </h2>
                    <p style="font-size: 18px; color: rgba(255,255,255,0.7); line-height: 1.7; margin: 0 0 48px 0;">
                        Create your student account to start borrowing laboratory equipment and managing your reservations.
                    </p>
                </div>

                <div class="animate-fade-in-up delay-200">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <svg width="22" height="22" fill="none" stroke="#4ade80" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <h3 style="font-size: 16px; font-weight: 600; color: white; margin: 0 0 4px 0;">Borrow Equipment</h3>
                            <p style="font-size: 14px; color: rgba(255,255,255,0.6); margin: 0; line-height: 1.5;">Request and borrow lab equipment with ease</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <svg width="22" height="22" fill="none" stroke="#86efac" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 style="font-size: 16px; font-weight: 600; color: white; margin: 0 0 4px 0;">Reserve Resources</h3>
                            <p style="font-size: 14px; color: rgba(255,255,255,0.6); margin: 0; line-height: 1.5;">Book rooms and equipment ahead of time</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <svg width="22" height="22" fill="none" stroke="#22c55e" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 style="font-size: 16px; font-weight: 600; color: white; margin: 0 0 4px 0;">Track History</h3>
                            <p style="font-size: 14px; color: rgba(255,255,255,0.6); margin: 0; line-height: 1.5;">View your borrowing history and active requests</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="animate-fade-in delay-400" style="position: absolute; bottom: 48px; left: 48px; right: 48px;">
                <p style="font-size: 14px; color: rgba(255,255,255,0.4); margin: 0;">
                    &copy; {{ date('Y') }} SEIMS. All rights reserved.
                </p>
            </div>
        </div>

        <!-- Right Registration Form Panel -->
        <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 48px; background: #f8fafc;">
            <div style="width: 100%; max-width: 420px;" class="animate-fade-in-up">
                <!-- Mobile Logo -->
                <div style="display: none; text-align: center; margin-bottom: 32px;" class="mobile-logo">
                    <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #16a34a, #22c55e); border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                        <img src="{{ asset('images/spup_logo.png') }}" alt="SPUP Logo" style="width: 56px; height: 56px; object-fit: contain;">
                    </div>
                    <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0;">SEIMS</h1>
                </div>

                <!-- Header -->
                <div style="margin-bottom: 32px;">
                    <h2 style="font-size: 28px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">Create Account</h2>
                    <p style="font-size: 15px; color: #64748b; margin: 0;">Register as a student to get started</p>
                </div>

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="alert-error animate-fade-in" style="margin-bottom: 24px;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span style="font-weight: 600;">Please fix the following errors:</span>
                        </div>
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Registration Form -->
                <form method="POST" action="{{ route('register') }}" x-data="{ loading: false }" @submit="loading = true">
                    @csrf
                    
                    <!-- Full Name -->
                    <div style="margin-bottom: 18px;">
                        <label for="name" style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">
                            Full Name
                        </label>
                        <div class="input-wrapper" style="position: relative;">
                            <svg class="input-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Juan Dela Cruz" class="form-input">
                        </div>
                    </div>

                    <!-- Student ID -->
                    <div style="margin-bottom: 18px;">
                        <label for="student_id" style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">
                            Student ID
                        </label>
                        <div class="input-wrapper" style="position: relative;">
                            <svg class="input-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                            </svg>
                            <input id="student_id" type="text" name="student_id" value="{{ old('student_id') }}" required placeholder="e.g. 2024-00001" class="form-input">
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div style="margin-bottom: 18px;">
                        <label for="email" style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">
                            Email Address
                        </label>
                        <div class="input-wrapper" style="position: relative;">
                            <svg class="input-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="name@email.com" class="form-input">
                        </div>
                    </div>
                    
                    <!-- Password -->
                    <div style="margin-bottom: 18px;">
                        <label for="password" style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">
                            Password
                        </label>
                        <div class="input-wrapper" style="position: relative;">
                            <svg class="input-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimum 8 characters" class="form-input">
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div style="margin-bottom: 28px;">
                        <label for="password_confirmation" style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">
                            Confirm Password
                        </label>
                        <div class="input-wrapper" style="position: relative;">
                            <svg class="input-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Re-enter your password" class="form-input">
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn-primary" :disabled="loading" style="min-height: 56px;">
                        <span x-show="!loading" class="btn-content">
                            Create Account
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink: 0;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </span>
                        <span x-show="loading" style="display:none" class="btn-content">
                            <div class="spinner"></div>
                            Creating account...
                        </span>
                    </button>
                </form>

                <!-- Divider -->
                <div style="display: flex; align-items: center; gap: 16px; margin: 32px 0;">
                    <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
                    <span style="font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">or</span>
                    <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
                </div>

                <!-- Sign In Link -->
                <div style="text-align: center;">
                    <p style="font-size: 14px; color: #64748b; margin: 0;">
                        Already have an account?
                        <a href="{{ route('login') }}" style="color: #16a34a; text-decoration: none; font-weight: 600; transition: color 0.2s;" onmouseover="this.style.color='#15803d'" onmouseout="this.style.color='#16a34a'">
                            Sign in
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
