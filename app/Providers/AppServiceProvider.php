<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Fix MySQL key length for utf8mb4 encoding
        Schema::defaultStringLength(191);

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
}
