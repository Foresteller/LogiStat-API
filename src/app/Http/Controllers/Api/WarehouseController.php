<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\WarehouseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WarehouseController extends Controller
{
    public function __construct(protected WarehouseService $service)
    {
    }

    public function index()
    {
        $start = microtime(true);
        $isCached = Cache::has('warehouse_catalog');
        $catalog = $this->service->getCatalog();
        $executionTime = microtime(true) - $start;
        return new JsonResponse([
            'source' => $isCached ? 'Redis (Cache Hit)' : 'PostgreSQL (Cache Miss)',
            'execution_time_ms' => round($executionTime * 1000, 2),
            'data' => $catalog
        ]);
    }
}
