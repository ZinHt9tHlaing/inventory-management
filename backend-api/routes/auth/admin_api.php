<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\SupplierController;
use App\Http\Controllers\Api\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix("admin")->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // can only access admin and manager
    Route::middleware(['auth:sanctum', "role:admin,manager"])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });

    // admin only access

    Route::middleware(['auth:sanctum', "role:admin"])->group(function () {
        Route::apiResource('users', UserController::class);

        // admin can only delete supplier
        Route::delete("suppliers/{supplier}", [SupplierController::class, 'destroy'])->missing(function () {
            return response()->json([
                "message" => "Supplier not found",
            ], 404);
        });
    });

    // admin and manager only
    Route::middleware(['auth:sanctum', "role:admin,manager"])->group(function () {
        // view, create, update and can't delete
        Route::apiResource('suppliers', SupplierController::class)->except(['destroy']);
    });
});