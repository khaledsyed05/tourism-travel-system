<?php

use App\Http\Controllers\API\Dashboard\DestinationController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Dashboard\TourPackageController;

Route::middleware(['auth:api'])->group(function () {
    Route::get('/search', [TourPackageController::class, 'search']);
    Route::get('tour-packages/{tourPackage}', [TourPackageController::class, 'show']);
    Route::get('/destinations', [DestinationController::class, 'index']);
    Route::get('/destinations/{destinationId}/tour-packages', [DestinationController::class, 'tourPackagesBelongToDestination']);
    Route::post('/reviews', [ReviewController::class, 'store']);

});
