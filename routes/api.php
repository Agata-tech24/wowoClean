<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContainerController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/containers/search', [ContainerController::class, 'search']);
Route::get('/containers/{id}/logs', [ContainerController::class, 'logs']);
Route::apiResource('containers', ContainerController::class);
