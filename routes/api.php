<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ResponseFieldController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Response Fields Routes
Route::prefix('response-fields')->group(function () {
    // Public routes
    Route::get('/', [ResponseFieldController::class, 'index']);
    Route::get('/active', [ResponseFieldController::class, 'getActive']);
    Route::get('/grouped', [ResponseFieldController::class, 'grouped']);
    Route::get('/{responseField}', [ResponseFieldController::class, 'show']);
});
