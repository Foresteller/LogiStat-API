<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::disableQueryLog();

        User::factory()->count(50)->create();
        Category::factory()->count(10)->create();
        Warehouse::factory()->count(5)->create();
        Product::factory()->count(500)->create();

        $userIds = User::pluck('id')->toArray();
        $warehouseIds = Warehouse::pluck('id')->toArray();
        $products = Product::all();

        $nowString = date('Y-m-d H:i:s');

        $stocksData = [];
        foreach ($warehouseIds as $wId) {
            foreach ($products as $product) {
                $stocksData[] = [
                    'warehouse_id' => $wId,
                    'product_id' => $product->id,
                    'quantity' => rand(10, 1000),
                    'created_at' => $nowString,
                    'updated_at' => $nowString,
                ];
            }
        }
        DB::table('stocks')->insert($stocksData);

        $totalOrders = 50000;
        $chunkSize = 1000;
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        for ($i = 0; $i < $totalOrders; $i += $chunkSize) {
            $ordersChunk = [];
            $orderItemsChunk = [];

            for ($j = 0; $j < $chunkSize; $j++) {
                $orderId = $i + $j + 1;
                $warehouseId = $warehouseIds[array_rand($warehouseIds)];
                $status = $statuses[array_rand($statuses)];

                $randomDaysAgo = rand(1, 365);
                $orderCreatedAt = date('Y-m-d H:i:s', strtotime("-$randomDaysAgo days"));

                $ordersChunk[] = [
                    'id' => $orderId,
                    'user_id' => $userIds[array_rand($userIds)],
                    'warehouse_id' => $warehouseId,
                    'status' => $status,
                    'total_amount' => 0,
                    'created_at' => $orderCreatedAt,
                    'updated_at' => $nowString,
                ];

                $randomProducts = $products->random(rand(1, 3));
                $orderTotal = 0;

                foreach ($randomProducts as $product) {
                    $qty = rand(1, 5);
                    $price = $product->price;
                    $orderTotal += $price * $qty;

                    $orderItemsChunk[] = [
                        'order_id' => $orderId,
                        'product_id' => $product->id,
                        'count' => $qty,
                        'price' => $price,
                        'created_at' => $orderCreatedAt,
                        'updated_at' => $nowString,
                    ];
                }

                $ordersChunk[$j]['total_amount'] = $orderTotal;
            }

            DB::table('orders')->insert($ordersChunk);
            DB::table('order_items')->insert($orderItemsChunk);
        }
    }
}
