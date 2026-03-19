<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\ClientController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
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
