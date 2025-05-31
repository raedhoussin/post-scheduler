<?php


/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Laravel Post Scheduler API",
 *     description="API documentation",
 *     @OA\Contact(
 *         email="support@example.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local Server"
 * )
 */


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PlatformController;
use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AnalyticsController;



Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login',    [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile',     [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);

    });

});


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('platforms', PlatformController::class);
    Route::post('/platforms/toggle', [PlatformController::class, 'toggleUserPlatform']);
    Route::apiResource('posts', PostController::class);
    Route::post('/posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs');
    Route::post('/profile', [AuthController::class, 'updateProfile']);
    Route::get('/analytics/posts', [AnalyticsController::class, 'postsAnalytics']);

});