<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public API routes
Route::prefix('v1')->group(function () {
    // Health check endpoint
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'service' => 'Movie Auditions API'
        ]); 
    })->name('api.health');
    
    // Health check endpoint
    Route::get('/health-check', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'service' => 'Movie Auditions API'
        ]); 
    })->name('api.health-check');
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login'])->name('api.auth.login');
        Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register'])->name('api.auth.register');
        Route::post('/forgot-password', [App\Http\Controllers\API\AuthController::class, 'forgotPassword'])->name('api.auth.forgot-password');
        Route::post('/reset-password', [App\Http\Controllers\API\AuthController::class, 'resetPassword'])->name('api.auth.reset-password');
        
        // OTP routes for registration
        Route::post('/send-registration-otp', [App\Http\Controllers\API\OTPController::class, 'sendRegistrationOTP']);
        Route::post('/verify-registration-otp', [App\Http\Controllers\API\OTPController::class, 'verifyRegistrationOTP']);
        
        // OTP routes for password reset
        Route::post('/send-password-reset-otp', [App\Http\Controllers\API\OTPController::class, 'sendPasswordResetOTP']);
        Route::post('/verify-password-reset-otp', [App\Http\Controllers\API\OTPController::class, 'verifyPasswordResetOTP']);
        Route::post('/reset-password-otp', [App\Http\Controllers\API\OTPController::class, 'resetPassword']);
    });
});

// Protected API routes (require authentication)
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Authentication routes (protected)
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout'])->name('api.auth.logout');
        Route::get('/user', [App\Http\Controllers\API\AuthController::class, 'user'])->name('api.auth.user');
    });
    
    // Unified Audition routes
    Route::apiResource('auditions', App\Http\Controllers\API\AuditionController::class)->names([
        'index' => 'api.auditions.index',
        'store' => 'api.auditions.store',
        'show' => 'api.auditions.show',
        'update' => 'api.auditions.update',
        'destroy' => 'api.auditions.destroy',
    ]);
    
    // Unified Movie routes
    Route::apiResource('movies', App\Http\Controllers\API\Unified\MovieController::class)->names([
        'index' => 'api.movies.index',
        'store' => 'api.movies.store',
        'show' => 'api.movies.show',
        'update' => 'api.movies.update',
        'destroy' => 'api.movies.destroy',
    ]);
    
    // Unified User routes
    Route::apiResource('users', App\Http\Controllers\API\Unified\UserController::class)->names([
        'index' => 'api.users.index',
        'store' => 'api.users.store',
        'show' => 'api.users.show',
        'update' => 'api.users.update',
        'destroy' => 'api.users.destroy',
    ]);
    
    // Unified Role routes
    Route::apiResource('roles', App\Http\Controllers\API\Unified\RoleController::class)->names([
        'index' => 'api.roles.index',
        'store' => 'api.roles.store',
        'show' => 'api.roles.show',
        'update' => 'api.roles.update',
        'destroy' => 'api.roles.destroy',
    ]);
    
    // Settings routes
    Route::get('settings', [App\Http\Controllers\API\Admin\SettingController::class, 'index']);
    Route::put('settings', [App\Http\Controllers\API\Admin\SettingController::class, 'update']);
    
    // Profile routes
    Route::get('profile', [App\Http\Controllers\API\Admin\SettingController::class, 'profile']);
    Route::put('profile', [App\Http\Controllers\API\Admin\SettingController::class, 'updateProfile']);
    Route::put('profile/password', [App\Http\Controllers\API\Admin\SettingController::class, 'updateProfilePassword']);
    
    // Notification routes
    Route::post('notifications/device-token', [App\Http\Controllers\API\NotificationController::class, 'registerDeviceToken']);
    Route::get('notifications', [App\Http\Controllers\API\NotificationController::class, 'getUserNotifications']);
    Route::post('notifications/{id}/read', [App\Http\Controllers\API\NotificationController::class, 'markAsRead']);
});

// User Gallery routes (public access)
Route::prefix('v1/users/{userId}/gallery')->group(function () {
    Route::get('/', [App\Http\Controllers\API\UserGalleryController::class, 'index'])->name('api.users.gallery.index');
    Route::post('/', [App\Http\Controllers\API\UserGalleryController::class, 'store'])->name('api.users.gallery.store');
    Route::put('/', [App\Http\Controllers\API\UserGalleryController::class, 'update'])->name('api.users.gallery.update');
    Route::delete('/{imagePath}', [App\Http\Controllers\API\UserGalleryController::class, 'destroy'])->name('api.users.gallery.destroy');
});

// Swagger documentation route
Route::get('/swagger.json', function () {
    // In a real implementation, this would generate the Swagger JSON
    // For now, we'll return a basic structure
    return response()->json([
        "openapi" => "3.0.0",
        "info" => [
            "title" => "Movie Auditions API",
            "description" => "API Documentation for Movie Auditions Backend",
            "version" => "1.0.0"
        ],
        "servers" => [
            [
                "url" => url('/api/v1'),
                "description" => "Movie Auditions API Server"
            ]
        ],
        "components" => [
            "securitySchemes" => [
                "bearerAuth" => [
                    "type" => "http",
                    "scheme" => "bearer",
                    "bearerFormat" => "JWT"
                ]
            ]
        ],
        "paths" => []
    ]);
});