<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Email - {{ config('app.name', 'SEIMS') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(1.4); opacity: 0; }
        }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .pulse-ring {
            position: absolute;
            inset: -8px;
            border-radius: 50%;
            border: 3px solid #16a34a;
            animation: pulse-ring 2s ease-out infinite;
        }
    </style>
</head>
<body>
    <div style="width: 100%; max-width: 520px; padding: 40px 24px; text-align: center;">
        <!-- Icon -->
        <div class="animate-fade-in-up" style="margin-bottom: 28px;">
            <div style="width: 80px; height: 80px; margin: 0 auto; position: relative; display: flex; align-items: center; justify-content: center;">
                <div class="pulse-ring"></div>
                <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #16a34a, #15803d); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 32px; height: 32px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Title -->
        <div class="animate-fade-in-up delay-100" style="opacity: 0;">
            <h1 style="font-size: 26px; font-weight: 700; color: #0f172a; margin: 0 0 12px;">Verify Your Email</h1>
            <p style="font-size: 15px; color: #64748b; margin: 0 0 8px; line-height: 1.6;">
                Thanks for signing up! Before getting started, please verify your email address by clicking the link we sent to <strong style="color: #334155;">{{ auth()->user()->email }}</strong>.
            </p>
        </div>

        @if (session('status'))
        <div class="animate-fade-in-up" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 14px 16px; margin: 24px 0; display: flex; align-items: center; gap: 8px; justify-content: center;">
            <svg width="16" height="16" fill="none" stroke="#16a34a" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span style="font-size: 14px; color: #166534;">{{ session('status') }}</span>
        </div>
        @endif

        <!-- Resend Button -->
        <div class="animate-fade-in-up delay-200" style="opacity: 0; margin-top: 32px;">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" style="width: 100%; padding: 16px 24px; background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 14px rgba(22, 163, 74, 0.4); min-height: 56px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(22,163,74,0.5)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(22,163,74,0.4)'">
                    Resend Verification Email
                </button>
            </form>
        </div>

        <!-- Sign Out -->
        <div style="margin-top: 28px;">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background: none; border: none; color: #16a34a; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none;">
                    &larr; Sign out and try again later
                </button>
            </form>
        </div>
    </div>
</body>
</html>
