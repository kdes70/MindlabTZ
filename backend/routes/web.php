<?php

use Illuminate\Support\Facades\Route;

// Заглушка для всех web-запросов
Route::any('/{any}', function () {
    return response()->json(['error' => 'Web routes are disabled. Use API routes only.'], 404);
})->where('any', '.*');
