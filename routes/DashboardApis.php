<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\Dashboard\DestinationController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::post('/assign-role', [AdminController::class, 'assignRole']);
    Route::get('/roles', [AdminController::class, 'listRoles']);
});

Route::get('/destinations', [DestinationController::class, 'index']);
Route::middleware(['auth:api'])->group(function () {
    Route::get('/destinations/{destination}', [DestinationController::class, 'show']);
});

Route::middleware(['auth:api', 'role:travel_agent|admin'])->group(function () {
    Route::post('/destinations', [DestinationController::class, 'store']);
    Route::put('/destinations/{destination}', [DestinationController::class, 'update']);
    Route::delete('/destinations/{destination}', [DestinationController::class, 'destroy']);
    Route::patch('destinations/{destination}/toggle-published', [DestinationController::class, 'togglePublished']);
});
