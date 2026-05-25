<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Account Pending - {{ config('app.name', 'SEIS') }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * { box-sizing: border-box; }
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
            border: 3px solid #f59e0b;
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
                <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 32px; height: 32px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Title -->
        <div class="animate-fade-in-up delay-100" style="opacity: 0;">
            <h1 style="font-size: 26px; font-weight: 700; color: #0f172a; margin: 0 0 12px;">Account Pending Approval</h1>
            <p style="font-size: 15px; color: #64748b; margin: 0 0 8px; line-height: 1.6;">
                Your account has been created successfully! An administrator needs to review and approve your registration before you can access the system.
            </p>
            <p style="font-size: 14px; color: #94a3b8; margin: 0;">
                You'll be able to log in once your account is approved.
            </p>
        </div>

        <!-- Info Card -->
        <div class="animate-fade-in-up delay-200" style="opacity: 0; margin-top: 32px;">
            <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <div style="width: 40px; height: 40px; background: #ecfdf5; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 20px; height: 20px; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div style="text-align: left;">
                        <p style="font-size: 14px; font-weight: 600; color: #0f172a; margin: 0;">{{ auth()->user()->name }}</p>
                        <p style="font-size: 13px; color: #94a3b8; margin: 0;">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; padding-top: 16px; border-top: 1px solid #f1f5f9;">
                    <div>
                        <p style="font-size: 12px; color: #94a3b8; margin: 0;">Student ID</p>
                        <p style="font-size: 14px; font-weight: 600; color: #334155; margin: 4px 0 0;">{{ auth()->user()->student_id ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 12px; color: #94a3b8; margin: 0;">Status</p>
                        <p style="font-size: 14px; font-weight: 600; color: #d97706; margin: 4px 0 0;">Pending</p>
                    </div>
                    <div>
                        <p style="font-size: 12px; color: #94a3b8; margin: 0;">Registered</p>
                        <p style="font-size: 14px; font-weight: 600; color: #334155; margin: 4px 0 0;">{{ auth()->user()->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
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
