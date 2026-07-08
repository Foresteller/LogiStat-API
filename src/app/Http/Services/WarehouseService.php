<?php

namespace App\Http\Services;

use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WarehouseService
{
    private CONST string CACHE_KEY_PREFIX = 'warehouse_catalog';
    private CONST int TTL = 3600;
    public function getCatalog(): array
    {
        return Cache::remember(self::CACHE_KEY_PREFIX, self::TTL, function () {
            return Warehouse::with(['stocks.product'])->get()->toArray();
        });
    }
    public function updateStock(int $warehouseId, int $productId, int $newQuantity): void
    {
        Stock::where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->update(['quantity' => $newQuantity]);

        Cache::forget(self::CACHE_KEY_PREFIX);
    }

}

