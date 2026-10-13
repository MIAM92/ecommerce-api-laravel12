<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;

// 1. Rutas Públicas de Productos
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// 2. Rutas de Autenticación (JWT)
Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Rutas protegidas dentro de Auth (Requieren estar logueado)
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// 3. Rutas Protegidas de E-commerce (CRUD Admin de Productos y Órdenes de Compra)
Route::group(['middleware' => 'auth:api'], function () {
    // CRUD administrativo de productos
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Procesamiento de compras con Stripe e Historial
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/history', [OrderController::class, 'history']);
});
