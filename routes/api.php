<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QueryHistoryController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Social Auth
Route::prefix('auth')->group(function () {
    Route::get('/google/redirect', [SocialAuthController::class, 'googleRedirect']);
    Route::get('/google/callback', [SocialAuthController::class, 'googleCallback']);
    Route::get('/github/redirect', [SocialAuthController::class, 'githubRedirect']);
    Route::get('/github/callback', [SocialAuthController::class, 'githubCallback']);
});

// Password reset
Route::post('/forgot-password', [PasswordController::class, 'forgot']);
Route::post('/reset-password',  [PasswordController::class, 'reset']);

// Public
Route::get('/listing/{network}', [ListingController::class, 'fetch']);
Route::post('/reviews/analyze',  [ReviewController::class, 'analyze']);
Route::get('/reviews',           [ReviewController::class, 'fetch']);
Route::post('/reviews',          [ReviewController::class, 'create']);
Route::apiResource('networks',   NetworkController::class);
Route::apiResource('fields',     FieldController::class);

// Protégé — auth:api
Route::middleware('auth:api')->group(function () {

    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Client uniquement
    Route::middleware('role:client')->group(function () {
        Route::put('/client/profile',         [ClientController::class, 'updateProfile']);
        Route::put('/client/password',        [ClientController::class, 'updatePassword']);
        Route::delete('/client/account',      [ClientController::class, 'deleteAccount']);

        // Query History
        Route::get('/query-history',          [QueryHistoryController::class, 'index']);
        Route::delete('/query-history/{id}',  [QueryHistoryController::class, 'destroy']);
        Route::delete('/query-history',       [QueryHistoryController::class, 'destroyAll']);

        // Dashboard
        Route::get('/kpis',                   [DashboardController::class, 'kpis']);
        Route::get('/dashboard/requests',     [DashboardController::class, 'requests']);

        // Notifications
        Route::prefix('notifications')->group(function () {
            Route::get('/',                         [NotificationController::class, 'index']);
            Route::patch('/mark-all-seen',          [NotificationController::class, 'markAllSeen']);
            Route::patch('/{notification}/seen',    [NotificationController::class, 'markSeen']);
            Route::delete('/',                      [NotificationController::class, 'clearAll']);
            Route::delete('/{notification}',        [NotificationController::class, 'destroy']);
        });
    });

    // Admin uniquement
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/clients',          [AdminController::class, 'listClients']);
        Route::delete('/clients/{id}',  [AdminController::class, 'deleteClient']);
    });
});
