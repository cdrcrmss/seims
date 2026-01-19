<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Simple logout route for testing
Route::get('/logout-test', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout.test');

// Authentication Routes
require __DIR__.'/auth.php';

// Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/student/borrow', [StudentController::class, 'borrowItem'])->name('student.borrow');
    
    // User Profile Routes
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [DashboardController::class, 'updatePassword'])->name('profile.password.update');
    
    // Admin Routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        // User Management
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
        
        // System Management
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::get('/borrowings', [AdminController::class, 'borrowings'])->name('borrowings');
    });
    
    // Staff Routes (accessible by both staff and admin)
    Route::middleware(['staff_or_admin'])->prefix('staff')->name('staff.')->group(function () {
        // Item Management
        Route::get('/items', [StaffController::class, 'items'])->name('items.index');
        Route::get('/items/create', [StaffController::class, 'createItem'])->name('items.create');
        Route::post('/items', [StaffController::class, 'storeItem'])->name('items.store');
        Route::get('/items/{item}/edit', [StaffController::class, 'editItem'])->name('items.edit');
        Route::put('/items/{item}', [StaffController::class, 'updateItem'])->name('items.update');
        Route::delete('/items/{item}', [StaffController::class, 'deleteItem'])->name('items.delete');
        
        // Borrowing Management
        Route::get('/borrowings', [StaffController::class, 'borrowings'])->name('borrowings.index');
        Route::patch('/borrowings/{borrowing}/approve', [StaffController::class, 'approveBorrowing'])->name('borrowings.approve');
        Route::patch('/borrowings/{borrowing}/reject', [StaffController::class, 'rejectBorrowing'])->name('borrowings.reject');
        Route::patch('/borrowings/{borrowing}/issue', [StaffController::class, 'issueBorrowing'])->name('borrowings.issue');
        Route::patch('/borrowings/{borrowing}/return', [StaffController::class, 'returnBorrowing'])->name('borrowings.return');
        
        // Reports
        Route::get('/reports', [StaffController::class, 'reports'])->name('reports');
    });

    // Student Routes - simplified without closure middleware
    Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
        Route::get('/borrowings', [StudentController::class, 'borrowings'])->name('borrowings.index');
        Route::delete('/borrowings/{borrowing}/cancel', [StudentController::class, 'cancelRequest'])->name('borrowings.cancel');
    });
});
