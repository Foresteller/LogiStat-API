<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(OrderService $service)
    {
        $this->service = $service;
    }
    public function store(OrderRequest $request)
    {
        $data = $request->validated();
        $order = $this->service->createOrder($data);
        return new JsonResponse([
            'message' => 'Order created successfully and placed in queue.',
            'order_id' => $order->id,
            'status' => $order->status,
            'total_amount' => $order->total_amount
        ], 201);
    }
}
