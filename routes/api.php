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
    
    // Authentication routes would go here if needed
});

// Protected API routes (require authentication)
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Audition routes
    Route::apiResource('auditions', App\Http\Controllers\API\AuditionController::class);
    
    // Movie routes
    Route::apiResource('movies', App\Http\Controllers\API\MovieController::class);
    
    // Admin specific routes
    Route::prefix('admin')->group(function () {
        // Admin movie routes
        Route::apiResource('movies', App\Http\Controllers\API\Admin\MovieController::class);
        
        // Admin user routes
        Route::apiResource('users', App\Http\Controllers\API\Admin\UserController::class);
        
        // Admin role routes
        Route::apiResource('roles', App\Http\Controllers\API\Admin\RoleController::class);
        
        // Admin settings routes
        Route::get('settings', [App\Http\Controllers\API\Admin\SettingController::class, 'index']);
        Route::put('settings', [App\Http\Controllers\API\Admin\SettingController::class, 'update']);
        
        // Admin profile routes
        Route::get('profile', [App\Http\Controllers\API\Admin\SettingController::class, 'profile']);
        Route::put('profile', [App\Http\Controllers\API\Admin\SettingController::class, 'updateProfile']);
        Route::put('profile/password', [App\Http\Controllers\API\Admin\SettingController::class, 'updateProfilePassword']);
    });
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