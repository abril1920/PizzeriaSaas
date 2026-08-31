<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::middleware(['auth:sanctum', 'empresa'])->group(function (): void {
        Route::get('/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
        Route::apiResource('productos', \App\Http\Controllers\Api\ProductoController::class)->only(['index', 'store']);
    });
});
