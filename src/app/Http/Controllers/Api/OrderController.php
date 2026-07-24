<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Services\OrderService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    public function __construct(protected OrderService $service)
    {
    }

    #[OA\Post(
        path: '/api/orders',
        summary: 'Создание заказа(асинхронно)',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['user_id', 'warehouse_id', 'items'],
                properties: [
                    new OA\Property(
                        property: 'user_id',
                        type: 'integer',
                        example: 8
                    ),
                    new OA\Property(
                        property: 'warehouse_id',
                        type: 'integer',
                        example: 8
                    ),
                    new OA\Property(
                        property: 'items',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(
                                    property: 'product_id',
                                    type: 'integer',
                                    example: 88
                                ),
                                new OA\Property(
                                    property: 'count',
                                    type: 'integer',
                                    example: 7
                                )
                            ],
                            type: 'object'
                        ),
                    )
                ]
            ),
        ),
        tags: ['Orders'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Заказ успешно создан и отправлен в очередь',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Order created successfully'
                        ),
                        new OA\Property(
                            property: 'order_id',
                            type: 'integer',
                            example: 7
                        ),
                        new OA\Property(
                            property: 'status',
                            type: 'string',
                            example: 'pending'
                        ),
                        new OA\Property(
                            property: 'total_amount',
                            type: 'number',
                            example: 8888.77
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Ошибка валидации входящих данных'
            )
        ]
    )]
    public function store(OrderRequest $request): JsonResponse
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
