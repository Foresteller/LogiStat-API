<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\ReportRepository;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function getTopProducts(ReportRepository $repository): JsonResponse
    {
        $report = $repository->findTopProducts();

        return new JsonResponse([
            'data' => $report['data'],
            'explain_plan' => $report['explain'],
        ]);
    }
}
