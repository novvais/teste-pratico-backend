<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GatewayController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\TransactionController;

Route::post('/login', [AuthController::class, 'login']);

Route::post('/transactions', [CheckoutController::class, 'store']);

Route::middleware(['auth:sanctum', 'role:ADMIN'])->group(function () {
    Route::patch('/gateway/{id}', [GatewayController::class, 'update']);
    Route::get('/gateways', [GatewayController::class, 'index']);
});

Route::prefix('clients')->controller(ClientController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
});

Route::prefix('products')->controller(ProductController::class)->middleware(['auth:sanctum', 'role:ADMIN,MANAGER,FINANCE'])->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::patch('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

Route::prefix('users')->controller(UserController::class)->middleware(['auth:sanctum', 'role:ADMIN,MANAGER'])->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::patch('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

Route::prefix('transactions')->controller(TransactionController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/{id}/chargeback', 'chargeback')->middleware('role:ADMIN,FINANCE');
});