<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ResponseFieldController;
use App\Http\Controllers\Api\ReviewFieldController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\PasswordController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Response Fields Routes
Route::prefix('response-fields')->group(function () {
    // Public routes
    Route::get('/', [ResponseFieldController::class, 'index']);
    Route::get('/{responseField}', [ResponseFieldController::class, 'show']);
});
// Review Fields Routes
Route::prefix('review-fields')->group(function () {
    // Public routes
    Route::get('/', [ReviewFieldController::class, 'index']);
    Route::get('/{reviewField}', [ReviewFieldController::class, 'show']);
});


Route::post('/networks', [NetworkController::class, 'store']);
Route::get('/networks', [NetworkController::class, 'index']);
Route::delete('/networks/{id}', [NetworkController::class, 'destroy']);

Route::prefix('clients')->group(function () {
    Route::post('/register', [ClientController::class, 'register']);
    Route::post('/login',    [ClientController::class, 'login']);

    Route::middleware('auth:clients')->group(function () {
        Route::get('/me',      [ClientController::class, 'me']);
        Route::post('/logout', [ClientController::class, 'logout']);
    });
});

Route::prefix('auth')->group(function () {
    // Google
    Route::get('/google/redirect', [SocialAuthController::class, 'googleRedirect']);
    Route::get('/google/callback', [SocialAuthController::class, 'googleCallback']);
    //Git
    Route::get('/github/redirect', [SocialAuthController::class, 'githubRedirect']);
    Route::get('/github/callback', [SocialAuthController::class, 'githubCallback']);
});
Route::post('/clients/forgot-password', [PasswordController::class, 'forgot']);
Route::post('/clients/reset-password', [PasswordController::class, 'reset']);
