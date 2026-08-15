<?php

use App\Http\Controllers\Api\Admin\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix("admin")->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // can only access admin and manager
    Route::middleware(['auth:sanctum', "role:admin,manager"])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});
