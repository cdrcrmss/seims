<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Help - {{ config('app.name', 'SEIMS') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/spup_logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
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
    </style>
</head>
<body>
    <div style="width: 100%; max-width: 480px; background: white; border-radius: 16px; padding: 32px 24px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #16a34a, #15803d); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <svg style="width: 28px; height: 28px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 style="font-size: 22px; font-weight: 700; color: #0f172a; margin: 0 0 8px;">Forgot your password?</h1>
            <p style="font-size: 14px; color: #64748b; margin: 0; line-height: 1.6;">
                Password resets are handled by your system administrator. Please contact the admin office and ask them to reset your account password.
            </p>
        </div>

        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
            <p style="font-size: 13px; font-weight: 600; color: #166534; margin: 0 0 8px;">What to bring / tell the admin:</p>
            <ul style="font-size: 13px; color: #15803d; margin: 0; padding-left: 18px; line-height: 1.6;">
                <li>Your full name</li>
                <li>Your registered email address</li>
                <li>Your student ID or staff ID (if applicable)</li>
            </ul>
        </div>

        <a href="{{ route('login') }}" style="display: block; text-align: center; padding: 14px 24px; background: linear-gradient(135deg, #16a34a, #15803d); color: white; text-decoration: none; border-radius: 12px; font-size: 15px; font-weight: 600;">
            Back to Sign In
        </a>
    </div>
</body>
</html>
