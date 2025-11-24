<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Authentication Routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Use Laravel's built-in authentication routes
Auth::routes();

// Home route (fallback)
Route::get('/home', function () {
    return redirect()->route('admin.dashboard');
})->name('home');

// Admin Routes (Protected)
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Movies Routes
    Route::resource('movies', App\Http\Controllers\Admin\MovieController::class)->names([
        'index' => 'admin.movies.index',
        'create' => 'admin.movies.create',
        'store' => 'admin.movies.store',
        'show' => 'admin.movies.show',
        'edit' => 'admin.movies.edit',
        'update' => 'admin.movies.update',
        'destroy' => 'admin.movies.destroy',
    ]);
    
    // Auditions Routes
    Route::resource('auditions', App\Http\Controllers\Admin\AuditionController::class)->names([
        'index' => 'admin.auditions.index',
        'create' => 'admin.auditions.create',
        'store' => 'admin.auditions.store',
        'show' => 'admin.auditions.show',
        'edit' => 'admin.auditions.edit',
        'update' => 'admin.auditions.update',
        'destroy' => 'admin.auditions.destroy',
    ]);
    
    // Users Routes
    Route::resource('users', App\Http\Controllers\Admin\UserController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'show' => 'admin.users.show',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);
    
    // Settings Routes
    Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.index');
    Route::put('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');
    Route::put('/settings/password', [App\Http\Controllers\Admin\SettingController::class, 'updatePassword'])->name('admin.settings.update-password');
});