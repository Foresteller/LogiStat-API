<?php

use Illuminate\Support\Facades\Route;

Route::get('/order', [\App\Http\Controllers\Api\OrderController::class, 'store']);
