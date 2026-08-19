<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::disableQueryLog();
        $users = User::factory()->count(50)->create();
        $warehouses = Warehouse::factory()->count(10)->create();
        $categories = Category::factory()->count(5)->create();
        $products = Product::factory()
            ->count(500)
            ->create([
                'category_id' => fn () => $categories->pluck('id')->random(),
            ]
            );
        $productsPrice = $products->pluck('price', 'id')->toArray();
        $warehouseIds = $warehouses->pluck('id')->toArray();
        $productIds = $products->pluck('id')->toArray();
        $usersIds = $users->pluck('id')->toArray();
        $timeStamps = now()->toDateTimeString();
        $stocksData = [];
        foreach ($warehouseIds as $warehouseId) {
            foreach ($productIds as $productId) {
                $stocksData[] = [
                    'warehouse_id' => $warehouseId,
                    'product_id' => $productId,
                    'quantity' => rand(1, 3),
                    'created_at' => $timeStamps,
                    'updated_at' => $timeStamps,
                ];
            }
        }
        DB::table('stocks')->insert($stocksData);

        $batchChunks = 5000;
        $totalChunks = 50000;
        $statuses = [
            'pending',
            'processing',
            'shipped',
            'delivered',
            'cancelled',
        ];
        for ($i = 0; $i < $totalChunks; $i += $batchChunks) {
            $batchOrders = [];
            $batchOrderItems = [];
            for ($j = 0; $j < $batchChunks; $j++) {
                $orderTotal = 0;
                $itemsCount = rand(1, 3);
                $pickedProducts = (array) array_rand($productIds, $itemsCount);
                $orderId = $i + $j + 1;
                $createdAt = now()->subDays(rand(1, 365))->toDateTimeString();
                foreach ($pickedProducts as $pIndex) {
                    $count = rand(1, 5);
                    $price = $productsPrice[$productIds[$pIndex]];
                    $orderTotal += $count * $price;

                    $batchOrderItems[] = [
                        'product_id' => $productIds[$pIndex],
                        'order_id' => $orderId,
                        'count' => $count,
                        'price' => $price,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];
                }
                $batchOrders[] = [
                    'id' => $orderId,
                    'user_id' => $usersIds[array_rand($usersIds)],
                    'warehouse_id' => $warehouseIds[array_rand($warehouseIds)],
                    'status' => $statuses[array_rand($statuses)],
                    'total_amount' => $orderTotal,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }
            DB::table('orders')->insert($batchOrders);
            DB::table('order_items')->insert($batchOrderItems);
        }
    }
}
