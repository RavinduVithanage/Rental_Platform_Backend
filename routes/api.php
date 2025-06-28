<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RentalItemController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/rental-items', [RentalItemController::class, 'index']);
Route::get('/rental-items/{id}', [RentalItemController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/rental-items', [RentalItemController::class, 'store']);
    Route::put('/rental-items/{id}', [RentalItemController::class, 'update']);
    Route::delete('/rental-items/{id}', [RentalItemController::class, 'destroy']);
    
    // Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/pending-items', [RentalItemController::class, 'pendingItems']);
        Route::put('/rental-items/{id}/status', [RentalItemController::class, 'updateStatus']);
    });
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('roles');
});