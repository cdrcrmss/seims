<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\PublicFileController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
require __DIR__.'/auth.php';

// Account pending approval page (must be outside the 'approved' middleware)
Route::middleware(['auth'])->group(function () {
    Route::get('/account/pending', function () {
        if (auth()->user()->is_approved) {
            return redirect()->route('dashboard');
        }
        return view('auth.pending-approval');
    })->name('account.pending');
});

// Dashboard Routes
Route::middleware(['auth', 'approved'])->group(function () {
    Route::get('/files/{path}', [PublicFileController::class, 'show'])
        ->where('path', '.*')
        ->name('storage.public.show');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Student Borrow Routes (with rate limiting)
    Route::get('/student/borrow', [StudentController::class, 'borrowForm'])
        ->name('student.borrow.form')
        ->middleware('throttle:borrow-page');
    Route::post('/student/borrow', [StudentController::class, 'borrowItem'])
        ->name('student.borrow')
        ->middleware('throttle:borrow-submit');
    
    // User Profile Routes
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [DashboardController::class, 'updatePassword'])->name('profile.password.update');
    
    // Admin Routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        // User Management
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::get('/users/search', [AdminController::class, 'searchUsers'])->name('users.search');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
        Route::patch('/users/{user}/approve', [AdminController::class, 'approveUser'])->name('users.approve');
        Route::patch('/users/{user}/reject', [AdminController::class, 'rejectUser'])->name('users.reject');
        Route::patch('/users/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('users.reset-password');
        
        // System Management
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::get('/borrowings', [AdminController::class, 'borrowings'])->name('borrowings');
        Route::patch('/borrowings/{borrowing}/archive', [AdminController::class, 'archiveBorrowing'])->name('borrowings.archive');
        Route::patch('/borrowings/{borrowing}/unarchive', [AdminController::class, 'unarchiveBorrowing'])->name('borrowings.unarchive');
    });
    
    // Staff Routes (accessible by both staff and admin)
    Route::middleware(['staff_or_admin'])->prefix('staff')->name('staff.')->group(function () {
        // Item Management
        Route::get('/items', [StaffController::class, 'items'])->name('items.index');
        Route::get('/items/search', [StaffController::class, 'searchAllItems'])->name('items.search');
        Route::get('/items/trash', [StaffController::class, 'trashedItems'])->name('items.trash');
        Route::get('/items/create', [StaffController::class, 'createItem'])->name('items.create');
        Route::post('/items', [StaffController::class, 'storeItem'])->name('items.store');
        Route::get('/items/{item}/edit', [StaffController::class, 'editItem'])->name('items.edit');
        Route::put('/items/{item}', [StaffController::class, 'updateItem'])->name('items.update');
        Route::delete('/items/{item}', [StaffController::class, 'deleteItem'])->name('items.delete');
        Route::post('/items/{item}/restore', [StaffController::class, 'restoreItem'])->name('items.restore');
        Route::delete('/items/{item}/force-delete', [StaffController::class, 'forceDeleteItem'])->name('items.force-delete');
        Route::post('/items/bulk-import', [StaffController::class, 'bulkImportItems'])->name('items.bulk-import');
        Route::get('/items/{item}/units', [StaffController::class, 'getItemUnits'])->name('items.units');
        Route::patch('/items/{item}/units/{unit}', [StaffController::class, 'updateUnitStatus'])->name('items.units.update');

        // Direct Borrowing (Staff/Admin can borrow items directly)
        Route::get('/borrow', [StaffController::class, 'borrowForm'])->name('borrow.form');
        Route::post('/borrow', [StaffController::class, 'borrowItem'])->name('borrow');
        Route::get('/api/search-items', [StaffController::class, 'searchItems'])->name('api.search-items');
        Route::get('/api/trashed-items', [StaffController::class, 'searchTrashedItems'])->name('api.trashed-items');

        // Borrowing Management
        Route::get('/borrowings', [StaffController::class, 'borrowings'])->name('borrowings.index');
        Route::patch('/borrowings/{borrowing}/approve', [StaffController::class, 'approveBorrowing'])->name('borrowings.approve');
        Route::patch('/borrowings/{borrowing}/reject', [StaffController::class, 'rejectBorrowing'])->name('borrowings.reject');
        Route::patch('/borrowings/{borrowing}/issue', [StaffController::class, 'issueBorrowing'])->name('borrowings.issue');
        Route::patch('/borrowings/{borrowing}/return', [StaffController::class, 'returnBorrowing'])->name('borrowings.return');
        Route::patch('/borrowings/{borrowing}/approve-extension', [StaffController::class, 'approveExtension'])->name('borrowings.approve-extension');
        Route::patch('/borrowings/{borrowing}/reject-extension', [StaffController::class, 'rejectExtension'])->name('borrowings.reject-extension');

        // Reports
        Route::get('/reports', [StaffController::class, 'reports'])->name('reports');
    });

    // Student Routes
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/borrowings', [StudentController::class, 'borrowings'])->name('borrowings.index');
        Route::delete('/borrowings/{borrowing}/cancel', [StudentController::class, 'cancelRequest'])->name('borrowings.cancel');
        Route::post('/borrowings/{borrowing}/extend', [StudentController::class, 'requestExtension'])->name('borrowings.extend');
        Route::get('/api/search-items', [StudentController::class, 'searchItems'])->name('api.search-items');
    });

    // Reservation & Scheduling Module (Conflict Detective)
    Route::prefix('reservations')->name('reservations.')->group(function () {
        Route::middleware(['staff_or_admin'])->group(function () {
            Route::get('/', [ReservationController::class, 'index'])->name('index');
        });
        Route::get('/calendar', [ReservationController::class, 'calendar'])->name('calendar');
        Route::get('/create', [ReservationController::class, 'create'])->middleware('throttle:reservation-page')->name('create');
        Route::post('/', [ReservationController::class, 'store'])->middleware('throttle:reservation-submit')->name('store');
        Route::post('/check-availability', [ReservationController::class, 'checkAvailability'])->middleware('throttle:availability-check')->name('check-availability');
        
        // Staff/Admin actions
        Route::middleware(['staff_or_admin'])->group(function () {
            Route::patch('/{reservation}/approve', [ReservationController::class, 'approve'])->name('approve');
            Route::patch('/{reservation}/reject', [ReservationController::class, 'reject'])->name('reject');
        });
        
        // Cancel own reservation
        Route::patch('/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('cancel');
    });

    // Maintenance & Condition Monitoring Module (Predictive Alerts)
    Route::middleware(['staff_or_admin'])->prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/', [MaintenanceController::class, 'index'])->name('index');
        Route::get('/dashboard', [MaintenanceController::class, 'dashboard'])->name('dashboard');
        Route::get('/create', [MaintenanceController::class, 'create'])->name('create');
        Route::post('/', [MaintenanceController::class, 'store'])->name('store');
        Route::patch('/{maintenance}/complete', [MaintenanceController::class, 'complete'])->name('complete');
        Route::post('/generate-alerts', [MaintenanceController::class, 'generatePredictiveAlerts'])->name('generate-alerts');
        Route::post('/export', [MaintenanceController::class, 'exportReport'])->name('export');
    });


    // Predictive Analytics Dashboard (Reporting & Predictive Module)
    Route::middleware(['staff_or_admin'])->prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])->name('index');
        Route::get('/demand-forecast', [AnalyticsController::class, 'demandForecast'])->name('demand-forecast');
        Route::get('/utilization', [AnalyticsController::class, 'utilization'])->name('utilization');
        Route::get('/maintenance-predictions', [AnalyticsController::class, 'maintenancePredictions'])->name('maintenance-predictions');
        Route::get('/procurement', [AnalyticsController::class, 'procurement'])->name('procurement');
        Route::get('/items/{item}', [AnalyticsController::class, 'itemAnalytics'])->name('item-analytics');
        Route::post('/export', [AnalyticsController::class, 'exportReport'])->name('export');
    });

    // QR Code Module (Digital Validation & Tracking)
    Route::prefix('qr')->name('qr.')->group(function () {
        // Scanner and lookup available to all authenticated users
        Route::get('/scanner', [QrCodeController::class, 'scanner'])->name('scanner');
        Route::get('/lookup/{item?}', [QrCodeController::class, 'lookup'])->name('lookup');

        // Generate and batch-generate restricted to staff/admin
        Route::middleware(['staff_or_admin'])->group(function () {
            Route::get('/generate/{item}', [QrCodeController::class, 'generate'])->name('generate');
            Route::post('/batch-generate', [QrCodeController::class, 'batchGenerate'])->name('batch-generate');
        });
    });
});
