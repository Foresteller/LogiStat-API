<?php

use Illuminate\Support\Facades\Route;

Route::get('/order', [\App\Http\Controllers\OrderController::class, 'createOrder']);
