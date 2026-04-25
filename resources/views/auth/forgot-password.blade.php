<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - {{ config('app.name', 'SEIMS') }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet">
    
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
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }

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
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(22, 163, 74, 0.5);
        }
        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
    </style>
</head>
<body>
    <div style="width: 100%; max-width: 480px; padding: 40px 24px;" x-data="{ loading: false }">
        <!-- Logo -->
        <div class="animate-fade-in-up" style="text-align: center; margin-bottom: 32px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #16a34a, #15803d); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <svg style="width: 28px; height: 28px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
            <h1 style="font-size: 24px; font-weight: 700; color: #0f172a; margin: 0 0 8px;">Forgot Password</h1>
            <p style="font-size: 14px; color: #64748b; margin: 0; line-height: 1.5;">Enter your email address and we'll send you a link to reset your password.</p>
        </div>

        <!-- Status Message -->
        @if (session('status'))
        <div class="animate-fade-in-up" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 14px 16px; margin-bottom: 20px;">
            <p style="font-size: 14px; color: #166534; margin: 0;">{{ session('status') }}</p>
        </div>
        @endif

        <!-- Error Messages -->
        @if ($errors->any())
        <div class="animate-fade-in-up" style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 14px 16px; margin-bottom: 20px;">
            @foreach ($errors->all() as $error)
            <p style="font-size: 14px; color: #991b1b; margin: 0;">{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('password.email') }}" 
              @submit="loading = true"
              class="animate-fade-in-up delay-100" style="opacity: 0;">
            @csrf

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 8px;">Email Address</label>
                <div class="input-wrapper" style="position: relative;">
                    <svg class="input-icon" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                    </svg>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="you@example.com" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn-primary" :disabled="loading" style="margin-bottom: 20px; min-height: 56px;">
                <span x-show="!loading">Send Reset Link</span>
                <span x-show="loading" x-cloak>Sending...</span>
            </button>
        </form>

        <!-- Back to Login -->
        <div class="animate-fade-in-up delay-200" style="opacity: 0; text-align: center;">
            <a href="{{ route('login') }}" style="font-size: 14px; color: #16a34a; text-decoration: none; font-weight: 500;">
                &larr; Back to Sign In
            </a>
        </div>
    </div>
</body>
</html>
