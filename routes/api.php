<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth:sanctum', 'resolve.tenant'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/test', function () {
        return response()->json(['message' => 'Tenant resolved successfully']);
    });

    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);

    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store']);

    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/orders/{order}/pay', [OrderController::class, 'pay']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
});
