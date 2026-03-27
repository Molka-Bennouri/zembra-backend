<?php

use App\Http\Controllers\ListingController;
use App\Http\Controllers\ScrapingHistoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ResponseFieldController;
use App\Http\Controllers\Api\ReviewFieldController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\ClientController;


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

Route::get('/listing/{network}', [ListingController::class, 'fetch']);

Route::get('/history', [ScrapingHistoryController::class, 'index']);
Route::delete('/history/{id}', [ScrapingHistoryController::class, 'destroy']);
Route::delete('/history', [ScrapingHistoryController::class, 'destroyAll']);
