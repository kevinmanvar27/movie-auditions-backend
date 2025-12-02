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
    
    // Movies Routes - Require manage_movies permission
    Route::middleware('permission:manage_movies')->group(function () {
        Route::resource('movies', App\Http\Controllers\Admin\MovieController::class)->names([
            'index' => 'admin.movies.index',
            'create' => 'admin.movies.create',
            'store' => 'admin.movies.store',
            'show' => 'admin.movies.show',
            'edit' => 'admin.movies.edit',
            'update' => 'admin.movies.update',
            'destroy' => 'admin.movies.destroy',
        ]);
    });
    
    // Auditions Routes - Require manage_auditions permission
    Route::middleware('permission:manage_auditions')->group(function () {
        Route::resource('auditions', App\Http\Controllers\Admin\AuditionController::class)->names([
            'index' => 'admin.auditions.index',
            'create' => 'admin.auditions.create',
            'store' => 'admin.auditions.store',
            'show' => 'admin.auditions.show',
            'edit' => 'admin.auditions.edit',
            'update' => 'admin.auditions.update',
            'destroy' => 'admin.auditions.destroy',
        ]);
    });
    
    // Users Routes - Require manage_users permission
    Route::middleware('permission:manage_users')->group(function () {
        Route::resource('users', App\Http\Controllers\Admin\UserController::class)->names([
            'index' => 'admin.users.index',
            'create' => 'admin.users.create',
            'store' => 'admin.users.store',
            'show' => 'admin.users.show',
            'edit' => 'admin.users.edit',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ]);
    });
    
    // Roles Routes - Require manage_roles permission
    Route::middleware('permission:manage_roles')->group(function () {
        Route::resource('roles', App\Http\Controllers\Admin\RoleController::class)->names([
            'index' => 'admin.roles.index',
            'create' => 'admin.roles.create',
            'store' => 'admin.roles.store',
            'show' => 'admin.roles.show',
            'edit' => 'admin.roles.edit',
            'update' => 'admin.roles.update',
            'destroy' => 'admin.roles.destroy',
        ]);
    });
    
    // Settings Routes - Require manage_settings permission
    Route::middleware('permission:manage_settings')->group(function () {
        Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.index');
        Route::put('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');
    });
    
    // Profile Routes - Available to all authenticated users
    Route::get('/profile', [App\Http\Controllers\Admin\SettingController::class, 'profile'])->name('admin.profile');
    Route::put('/profile', [App\Http\Controllers\Admin\SettingController::class, 'updateProfile'])->name('admin.profile.update');
    Route::put('/profile/password', [App\Http\Controllers\Admin\SettingController::class, 'updateProfilePassword'])->name('admin.profile.update-password');
});