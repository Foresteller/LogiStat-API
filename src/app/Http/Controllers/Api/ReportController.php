<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function getTopProducts(): JsonResponse
    {
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.name as product_name',
                DB::raw('SUM(order_items.count) as total_quantity'),
                DB::raw('SUM(order_items.count * order_items.price) as total_revenue')
            )
            ->where('orders.status', '=', 'delivered')
            ->where('orders.created_at', '>=', now()->subMonths(6))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(10);

        $results = $query->get();
        $explainPlan = $query->explain();

        return new JsonResponse([
            'data' => $results,
            'explain_plan' => $explainPlan
        ]);
    }
}
