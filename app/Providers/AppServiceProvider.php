<?php

namespace App\Providers;

use App\Support\MailConfig;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        config(['mail.default' => MailConfig::resolveDefaultMailer()]);

        $this->configureUploadsDisk();

        // Fix MySQL key length for utf8mb4 encoding
        Schema::defaultStringLength(191);

        // Login brute-force protection: 5 attempts per email+IP per 15 minutes
        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower((string) $request->input('email', ''));
            $key = $email.'|'.$request->ip();

            return Limit::perMinutes(15, 5)
                ->by($key)
                ->response(function (Request $request, array $headers) {
                    $retryAfter = (int) ($headers['Retry-After'] ?? 900);
                    $minutes = max(1, (int) ceil($retryAfter / 60));
                    $message = "Too many login attempts. Please wait {$minutes} minute(s) before trying again.";

                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => $message,
                            'retry_after' => $retryAfter,
                        ], 429, $headers);
                    }

                    return redirect()
                        ->route('login')
                        ->withErrors(['email' => $message])
                        ->withInput($request->only('email'));
                });
        });

        // Guest / registration abuse protection
        RateLimiter::for('guest', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });

        // Authenticated JSON/search endpoints (application-layer DDoS mitigation)
        RateLimiter::for('public-api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    $retryAfter = (int) ($headers['Retry-After'] ?? 60);
                    $message = 'Too many requests. Please slow down and try again shortly.';

                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => $message,
                            'retry_after' => $retryAfter,
                        ], 429, $headers);
                    }

                    return response($message, 429, $headers);
                });
        });

        // Rate limiters for borrowing form submissions
        RateLimiter::for('borrow-submit', function (Request $request) {
            return Limit::perMinutes(5, 3)->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return back()->withErrors([
                        'rate_limit' => 'Too many borrow requests. Please wait a few minutes before trying again.',
                    ])->withInput();
                });
        });

        RateLimiter::for('borrow-page', function (Request $request) {
            return Limit::perMinute(20)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiters for reservation form submissions
        RateLimiter::for('reservation-submit', function (Request $request) {
            return Limit::perMinutes(5, 5)->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return back()->withErrors([
                        'rate_limit' => 'Too many reservation requests. Please wait a few minutes before trying again.',
                    ])->withInput();
                });
        });

        RateLimiter::for('reservation-page', function (Request $request) {
            return Limit::perMinute(20)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('availability-check', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        // Register policies
        Gate::policy(\App\Models\Reservation::class, \App\Policies\ReservationPolicy::class);
        Gate::policy(\App\Models\Borrowing::class, \App\Policies\BorrowingPolicy::class);
        Gate::policy(\App\Models\Item::class, \App\Policies\ItemPolicy::class);
        Gate::policy(\App\Models\MaintenanceRecord::class, \App\Policies\MaintenanceRecordPolicy::class);
        Gate::policy(\App\Models\ProcurementRequest::class, \App\Policies\ProcurementRequestPolicy::class);

        // Share notifications with the app layout
        View::composer('layouts.app', function ($view) {
            if ($user = auth()->user()) {
                $headerNotifications = \App\Models\Notification::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
                $unreadNotifCount = \App\Models\Notification::where('user_id', $user->id)
                    ->whereNull('read_at')
                    ->count();
            } else {
                $headerNotifications = collect();
                $unreadNotifCount = 0;
            }
            $view->with('headerNotifications', $headerNotifications)
                 ->with('unreadNotifCount', $unreadNotifCount);
        });
    }

    /**
     * Use Laravel Cloud object storage for uploads when a bucket is attached.
     */
    protected function configureUploadsDisk(): void
    {
        if (env('FILESYSTEM_UPLOADS_DISK')) {
            return;
        }

        if (! filled(env('AWS_BUCKET'))) {
            config(['filesystems.uploads_disk' => 'public']);

            return;
        }

        $default = env('FILESYSTEM_DISK');

        if ($default && $default !== 'local' && config("filesystems.disks.{$default}.driver") === 's3') {
            config(['filesystems.uploads_disk' => $default]);

            return;
        }

        foreach (array_keys(config('filesystems.disks', [])) as $disk) {
            if (config("filesystems.disks.{$disk}.driver") === 's3') {
                config(['filesystems.uploads_disk' => $disk]);

                return;
            }
        }

        config(['filesystems.uploads_disk' => 's3']);
    }
}
