<?php

namespace App\Http\Services;

use App\Jobs\ProcessOrderJob;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $productsIds = collect($data['items'])->pluck('product_id');
            $prices = Product::whereIn('id', $productsIds)->pluck(
                'price',
                'id'
            );
            $items = collect($data['items'])->map(
                function ($item) use ($prices) {
                    return [
                        'product_id' => $item['product_id'],
                        'count' => $item['count'],
                        'price' => $prices[$item['product_id']] ?? 0
                    ];
                }
            );
            $totalAmount = $items->sum(
                fn($item) => $item['count'] * $item['price']
            );

            $order = Order::create([
                'user_id' => $data['user_id'],
                'warehouse_id' => $data['warehouse_id'],
                'status' => 'pending',
                'total_amount' => $totalAmount
            ]);
            $order->items()->createMany($items);

            ProcessOrderJob::dispatch($order);

            return $order;
        });
    }
}
