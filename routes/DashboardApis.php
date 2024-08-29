<?php

use App\Http\Controllers\API\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::post('/users/{user}/role', [AdminController::class, 'assignRole']);
    Route::get('/roles', [AdminController::class, 'listRoles']);
});