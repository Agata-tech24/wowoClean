<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContainerController;

// Auth routes
Route::prefix('v1')->group(function () {

    // Login
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:api')->group(function () {

        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Gateway - semua bisa GET
        Route::prefix('gateway')->group(function () {
            Route::get('/containers', [ContainerController::class, 'index']);
            Route::get('/containers/search', [ContainerController::class, 'search']);
            Route::get('/containers/{id}', [ContainerController::class, 'show']);
            Route::get('/containers/{id}/logs', [ContainerController::class, 'logs']);

            // Hanya admin
            Route::middleware('role:admin')->group(function () {
                Route::post('/containers', [ContainerController::class, 'store']);
                Route::patch('/containers/{id}', [ContainerController::class, 'update']);
                Route::delete('/containers/{id}', [ContainerController::class, 'destroy']);
            });
        });
    });
});