<?php

use App\Http\Controllers\Api\User\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix("user")->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // can only access user
    Route::middleware(["user"])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});
