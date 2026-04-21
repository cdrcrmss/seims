<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SEIMS - Laboratory Management System</title>
    <meta http-equiv="refresh" content="0; url={{ route('login') }}">
    @vite(['resources/css/app.css'])
</head>
<body class="font-inter">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-green-700 mb-4">
                SEIMS
            </h1>
            <p class="text-gray-600 mb-4">Redirecting to login...</p>
            <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:from-blue-600 hover:to-purple-700 transition-colors">
                Continue to Login
            </a>
        </div>
    </div>
</body>
</html>