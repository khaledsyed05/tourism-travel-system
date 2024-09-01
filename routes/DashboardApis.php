<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\Dashboard\DestinationController;
use App\Http\Controllers\API\Dashboard\TourPackageController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::post('/assign-role', [AdminController::class, 'assignRole']);
    Route::get('/roles', [AdminController::class, 'listRoles']);
});

Route::middleware(['auth:api', 'role:travel_agent|admin'])->group(function () {
    Route::prefix('destinations')->group(function () {
        Route::get('/', [DestinationController::class, 'index']);
        Route::get('/{destination}', [DestinationController::class, 'show']);
        Route::post('/', [DestinationController::class, 'store']);
        Route::put('/{destination}', [DestinationController::class, 'update']);
        Route::delete('/{destination}', [DestinationController::class, 'destroy']);
        Route::patch('/{destination}/toggle-published', [DestinationController::class, 'togglePublished']);
    });
    
    Route::prefix('tour-packages')->group(function () {
        Route::get('/', [TourPackageController::class, 'index']);
        Route::get('/search', [TourPackageController::class, 'search']);
        Route::get('/{tourPackage}', [TourPackageController::class, 'show']);
        Route::post('/', [TourPackageController::class, 'store']);
        Route::put('/{tourPackage}', [TourPackageController::class, 'update']);
        Route::delete('/{tourPackage}', [TourPackageController::class, 'destroy']);
        Route::patch('/{tourPackage}/toggle-published', [TourPackageController::class, 'togglePublished']);
    });
});
