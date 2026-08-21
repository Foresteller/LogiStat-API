<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ReportRepository
{
    public function findTopProducts(): array
    {
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.name as product_name',
                DB::raw('SUM(order_items.count) as total_quantity'),
                DB::raw(
                    'SUM(order_items.count * order_items.price) as total_revenue'
                )
            )
            ->where('orders.status', '=', 'delivered')
            ->where('orders.created_at', '>=', now()->subMonths(6))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(10);

        $data = $query->get();
        $explain = $query->explain();

        return [
            'data' => $data,
            'explain' => $explain,
        ];
    }
}
