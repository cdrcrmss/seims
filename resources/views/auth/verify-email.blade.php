<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - SEIS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
            max-width: 480px;
            width: 100%;
            padding: 48px;
            text-align: center;
        }
        .icon-wrap {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(59,130,246,0.4); }
            50% { box-shadow: 0 0 0 20px rgba(59,130,246,0); }
        }
        .icon-wrap svg { width: 40px; height: 40px; color: white; }
        h1 { font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 12px; }
        .desc { color: #64748b; font-size: 15px; line-height: 1.6; margin-bottom: 32px; }
        .status {
            background: #ecfdf5; border: 1px solid #6ee7b7; border-radius: 12px;
            padding: 14px 16px; color: #065f46; font-size: 14px; margin-bottom: 24px;
            display: flex; align-items: center; gap: 8px;
        }
        .btn {
            display: inline-block; width: 100%; padding: 14px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white; border: none; border-radius: 12px;
            font-size: 15px; font-weight: 600; cursor: pointer;
            transition: all 0.2s; text-decoration: none;
        }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 8px 25px rgba(59,130,246,0.4); }
        .links { margin-top: 24px; }
        .links a { color: #3b82f6; text-decoration: none; font-size: 14px; }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>

        <h1>Verify Your Email</h1>
        <p class="desc">
            Thanks for signing up! Before getting started, please verify your email address by clicking the link we sent to <strong>{{ auth()->user()->email }}</strong>.
        </p>

        @if (session('status'))
            <div class="status">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn">Resend Verification Email</button>
        </form>

        <div class="links">
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: #3b82f6; cursor: pointer; font-size: 14px;">
                    Sign Out
                </button>
            </form>
        </div>
    </div>
</body>
</html>
