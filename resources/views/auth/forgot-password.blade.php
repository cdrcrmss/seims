<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - {{ config('app.name', 'SEIMS') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/spup_logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>
        * { box-sizing: border-box; }
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            min-height: 100vh;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }
        .form-input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            background: #fff;
            color: #1e293b;
        }
        .form-input:focus {
            outline: none;
            border-color: #16a34a;
            box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.1);
        }
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
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
        }
        .btn-primary:disabled { opacity: 0.7; cursor: not-allowed; }
    </style>
</head>
<body>
    <div style="width: 100%; max-width: 480px; background: white; border-radius: 16px; padding: 32px 24px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;" x-data="{ loading: false }">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #16a34a, #15803d); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <svg style="width: 28px; height: 28px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h1 style="font-size: 22px; font-weight: 700; color: #0f172a; margin: 0 0 8px;">Forgot your password?</h1>
            <p style="font-size: 14px; color: #64748b; margin: 0; line-height: 1.6;">
                Enter your administrator account email and we will send you a password reset link.
            </p>
        </div>

        @if (session('status'))
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 14px 16px; margin-bottom: 20px;">
            <p style="font-size: 14px; color: #166534; margin: 0;">{{ session('status') }}</p>
        </div>
        @endif

        @if (session('dev_reset_url'))
        <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 14px 16px; margin-bottom: 20px;">
            <p style="font-size: 13px; font-weight: 600; color: #92400e; margin: 0 0 8px;">Development reset link</p>
            <a href="{{ session('dev_reset_url') }}" style="font-size: 13px; color: #b45309; word-break: break-all;">{{ session('dev_reset_url') }}</a>
        </div>
        @endif

        @if ($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 14px 16px; margin-bottom: 20px;">
            @foreach ($errors->all() as $error)
            <p style="font-size: 14px; color: #991b1b; margin: 0;">{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" @submit="loading = true">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 8px;">Email address</label>
                <div style="position: relative;">
                    <svg class="input-icon" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                    <input type="email" name="email" value="{{ $email ?? old('email') }}" class="form-input" placeholder="admin@example.com" required autofocus>
                </div>
            </div>
            <button type="submit" class="btn-primary" :disabled="loading" style="margin-bottom: 16px;">
                <span x-show="!loading">Send reset link</span>
                <span x-show="loading" x-cloak>Sending...</span>
            </button>
        </form>

        <div style="text-align: center;">
            <a href="{{ route('login') }}" style="font-size: 14px; color: #16a34a; text-decoration: none; font-weight: 500;">&larr; Back to Sign In</a>
        </div>
    </div>
</body>
</html>
