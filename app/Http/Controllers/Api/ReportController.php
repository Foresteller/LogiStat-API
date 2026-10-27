<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\ReportRepository;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ReportController extends Controller
{
    #[OA\Get(
        path: '/api/reports/top-products',
        description: 'Возвращает данные отчёта и план выполнения SQL-запроса.',
        summary: 'Получить отчёт по популярным товарам',
        security: [['bearerAuth' => []]],
        tags: ['Reports'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Отчёт успешно получен',
                content: new OA\JsonContent(
                    required: ['data', 'explain_plan'],
                    properties: [
                        new OA\Property(
                            property: 'data',
                            description: 'Данные отчёта по популярным товарам',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object'
                            )
                        ),
                        new OA\Property(
                            property: 'explain_plan',
                            description: 'План выполнения SQL-запроса',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object'
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Пользователь не авторизован'
            ),
            new OA\Response(
                response: 403,
                description: 'Недостаточно прав для просмотра отчёта'
            ),
            new OA\Response(
                response: 500,
                description: 'Внутренняя ошибка сервера'
            ),
        ]
    )]

    public function getTopProducts(ReportRepository $repository): JsonResponse
    {
        $report = $repository->findTopProducts();

        return new JsonResponse([
            'data' => $report['data'],
            'explain_plan' => $report['explain'],
        ]);
    }
}
