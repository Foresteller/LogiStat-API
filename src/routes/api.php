<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

Route::post('/order', [OrderController::class, 'store']);
Route::get('/reports/top-products', [ReportController::class, 'getTopProducts']);
