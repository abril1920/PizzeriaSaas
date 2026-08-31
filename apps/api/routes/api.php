<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::middleware('throttle:register')->post('/register', [\App\Modules\Identity\Http\Controllers\AuthController::class, 'register']);
    Route::middleware('throttle:login')->post('/login', [\App\Modules\Identity\Http\Controllers\AuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'abilities:api', 'company'])->group(function (): void {
        Route::get('/me', [\App\Modules\Identity\Http\Controllers\AuthController::class, 'me']);
        Route::post('/logout', [\App\Modules\Identity\Http\Controllers\AuthController::class, 'logout']);
        Route::apiResource('productos', \App\Modules\Catalog\Http\Controllers\ProductController::class)->only(['index', 'store']);
    });
});
