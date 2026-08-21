<?php

namespace App\Http\Controllers\Api;

use App\Contracts\WarehouseCatalogInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class WarehouseController extends Controller
{
    public function __construct(protected WarehouseCatalogInterface $service) {}

    #[OA\Get(
        path: '/api/warehouses/catalog',
        description: 'Возвращает полный список складов с имеющимися остатками товаров. Данные обрабатываются по паттерну Cache-Aside через Redis',
        summary: 'Получение каталога складов с остатками товаров',
        tags: ['Warehouse'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Каталог успешно получен',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'source',
                            type: 'string',
                            example: 'Redis (Cache Hit)'
                        ),
                        new OA\Property(
                            property: 'execution_time_ms',
                            type: 'number',
                            format: 'float',
                            example: 18.2
                        ),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(
                                        property: 'id',
                                        type: 'integer',
                                        example: 7
                                    ),
                                    new OA\Property(
                                        property: 'name',
                                        type: 'string',
                                        example: 'Октябрьский'
                                    ),
                                    new OA\Property(
                                        property: 'address',
                                        type: 'string',
                                        example: 'г. Новосибирск, ул. Бориса Богаткова, д.17'
                                    ),
                                    new OA\Property(
                                        property: 'stocks',
                                        type: 'array',
                                        items: new OA\Items(
                                            properties: [
                                                new OA\Property(
                                                    property: 'id',
                                                    type: 'integer',
                                                    example: 87
                                                ),
                                                new OA\Property(
                                                    property: 'warehouse_id',
                                                    type: 'integer',
                                                    example: 8
                                                ),
                                                new OA\Property(
                                                    property: 'product_id',
                                                    type: 'integer',
                                                    example: 78
                                                ),
                                                new OA\Property(
                                                    property: 'quantity',
                                                    type: 'integer',
                                                    example: 7
                                                ),
                                            ],
                                            type: 'object'
                                        )
                                    ),
                                    new OA\Property(
                                        property: 'product',
                                        type: 'array',
                                        items: new OA\Items(
                                            properties: [
                                                new OA\Property(
                                                    property: 'id',
                                                    type: 'integer',
                                                    example: 17
                                                ),
                                                new OA\Property(
                                                    property: 'name',
                                                    type: 'string',
                                                    example: 'headphones'
                                                ),
                                                new OA\Property(
                                                    property: 'sku',
                                                    type: 'integer',
                                                    example: 'HEAD-PHONE-001'
                                                ),
                                                new OA\Property(
                                                    property: 'price',
                                                    type: 'number',
                                                    format: 'float',
                                                    example: 3999.99
                                                ),
                                            ],
                                            type: 'object'
                                        )
                                    ),
                                ],
                                type: 'object'
                            )
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        $start = microtime(true);
        $isCached = $this->service->hasCache();
        $catalog = $this->service->getCatalog();
        $executionTime = microtime(true) - $start;

        return new JsonResponse([
            'source' => $isCached ? 'Redis (Cache Hit)'
                : 'PostgreSQL (Cache Miss)',
            'execution_time_ms' => round($executionTime * 1000, 2),
            'data' => $catalog,
        ]);
    }
}
