<?php

use App\Http\Controllers\Api\ActiveLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use Illuminate\Support\Facades\Route;

// Авторизация и аутентификация
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

// Защищенные маршруты
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // Логи (только для пользователей с правами доступа)
    Route::get('logs', [ActiveLogController::class, 'index']);

    Route::resource('products', ProductController::class)
        ->only(['index', 'show', 'store', 'update', 'destroy'])
        ->middleware('can:manage-products');

    Route::resource('sales', SaleController::class)
        ->only(['index', 'show', 'store', 'update', 'destroy'])
        ->middleware('can:manage-sales');
});

// Публичные маршруты
Route::get('/', static function () {
    return response()->json(['message' => 'Welcome to the API!']);
});

