<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Services\WarehouseService;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/warehouses/catalog', [WarehouseController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::middleware('role:client')->post('/orders', [OrderController::class, 'store']);
    Route::middleware('role:warehouse_admin,manager')->get('/reports/top-products', [ReportController::class, 'getTopProducts']);
});
