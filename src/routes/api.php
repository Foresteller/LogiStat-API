<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Services\WarehouseService;
use Illuminate\Support\Facades\Route;

Route::post('/order', [OrderController::class, 'store']);

Route::get('/reports/top-products', [ReportController::class, 'getTopProducts']);

Route::get('/warehouses/catalog', [WarehouseController::class, 'index']);
