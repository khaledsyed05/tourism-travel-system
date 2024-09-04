<?php

use App\Http\Controllers\Api\Admin\AnalyticsController;
use App\Http\Controllers\Api\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'role:admin'])->group(function () {
    // Dashboard summary endpoint
    Route::get('/dashboard-summary', [DashboardController::class, 'summary']);

    // Analytics endpoints
    Route::get('/analytics/overview', [AnalyticsController::class, 'overview']);
    Route::get('/analytics/sales', [AnalyticsController::class, 'sales']);
    Route::get('/analytics/users', [AnalyticsController::class, 'users']);
    Route::get('/analytics/popular-destinations', [AnalyticsController::class, 'popularDestinations']);
    Route::get('/analytics/popular-packages', [AnalyticsController::class, 'popularPackages']);
});