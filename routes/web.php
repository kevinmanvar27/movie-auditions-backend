<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

// Authentication Routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Use Laravel's built-in authentication routes
Auth::routes();

// Home route (fallback)
Route::get('/home', function () {
    return redirect()->route('auditions.index');
})->name('home');

// Public user delete route (GET)
Route::get('/users/{id}/delete', function ($id) {
    $user = User::find($id);
    
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.'
        ], 404);
    }
    
    $user->delete();
    
    return response()->json([
        'success' => true,
        'message' => 'User deleted successfully.'
    ]);
})->name('users.delete');

// Payment Routes (Protected)
Route::middleware(['auth'])->group(function () {
    Route::post('/payment/audition/order', [App\Http\Controllers\PaymentController::class, 'createAuditionPaymentOrder'])->name('payment.audition.order');
    Route::post('/payment/movie/order', [App\Http\Controllers\PaymentController::class, 'createMoviePaymentOrder'])->name('payment.movie.order');
    Route::post('/payment/audition/verify', [App\Http\Controllers\PaymentController::class, 'verifyAuditionPaymentAndSubmit'])->name('payment.audition.verify');
    Route::post('/payment/movie/verify', [App\Http\Controllers\PaymentController::class, 'verifyMoviePaymentAndCreate'])->name('payment.movie.verify');
});

// Audition Routes (Protected) - Accessible to all authenticated users
Route::middleware(['auth'])->group(function () {
    Route::resource('auditions', App\Http\Controllers\AuditionController::class)->names([
        'index' => 'auditions.index',
        'create' => 'auditions.create',
        'store' => 'auditions.store',
        'show' => 'auditions.show',
    ]); 
    
    // Route for removing videos from auditions
    Route::delete('auditions/{audition}/remove-video', [App\Http\Controllers\AuditionController::class, 'removeVideo'])->name('auditions.remove-video');
    
    // Route for uploading new videos to auditions
    Route::post('auditions/{audition}/upload-videos', [App\Http\Controllers\AuditionController::class, 'uploadVideos'])->name('auditions.upload-videos');
    
    // AJAX route for fetching movie roles
    Route::get('movies/{movie}/roles', [App\Http\Controllers\AuditionController::class, 'getMovieRoles'])->name('movies.roles');
});

// Admin Routes (Protected)
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard')->middleware('permission:view_dashboard');
    
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
        
        // Route for updating audition status
        Route::post('auditions/{audition}/update-status', [App\Http\Controllers\Admin\MovieController::class, 'updateAuditionStatus'])->name('admin.auditions.update-status');
    });
    
    // Audition Routes - Require manage_auditions permission
    Route::middleware('permission:manage_auditions')->group(function () {
        // Add any specific audition management routes here in the future
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
    
    // Notification Routes - Require manage_notifications permission
    Route::middleware('permission:manage_notifications')->group(function () {
        Route::get('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications.index');
        Route::get('/notifications/create', [App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('admin.notifications.create');
        Route::post('/notifications/send', [App\Http\Controllers\Admin\NotificationController::class, 'send'])->name('admin.notifications.send');
        Route::get('/notifications/{id}', [App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('admin.notifications.show');
    });
    
    // Pages Routes - Require manage_pages permission (Super Admin only)
    Route::middleware('permission:manage_pages')->group(function () {
        Route::resource('pages', App\Http\Controllers\Admin\PageController::class)->names([
            'index' => 'admin.pages.index',
            'create' => 'admin.pages.create',
            'store' => 'admin.pages.store',
            'show' => 'admin.pages.show',
            'edit' => 'admin.pages.edit',
            'update' => 'admin.pages.update',
            'destroy' => 'admin.pages.destroy',
        ]);
        Route::get('pages/{page}/preview', [App\Http\Controllers\Admin\PageController::class, 'preview'])->name('admin.pages.preview');
    });
    
    // Profile Routes - Available to all authenticated users
    Route::get('/profile', [App\Http\Controllers\Admin\SettingController::class, 'profile'])->name('admin.profile');
    Route::put('/profile', [App\Http\Controllers\Admin\SettingController::class, 'updateProfile'])->name('admin.profile.update');
    Route::put('/profile/password', [App\Http\Controllers\Admin\SettingController::class, 'updateProfilePassword'])->name('admin.profile.update-password');
});

// Public Pages Route - MUST be at the end to avoid catching other routes
// This is a catch-all route for dynamic pages like /privacy-policy, /terms, etc.
Route::get('/{slug}', [App\Http\Controllers\PageController::class, 'show'])
    ->name('pages.show')
    ->where('slug', '[a-z0-9\-]+');