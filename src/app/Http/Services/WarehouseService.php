<?php

namespace App\Http\Services;

use App\Contracts\WarehouseCatalogInterface;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WarehouseService implements WarehouseCatalogInterface
{
    private const string CACHE_KEY_PREFIX = 'warehouse_catalog';
    private const int TTL = 3600;
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

        $this->clearCatalogCache();
    }
    public  function clearCatalogCache(): void
    {
        Cache::forget(self::CACHE_KEY_PREFIX);
    }
    public function hasCache(): bool
    {
        return Cache::has(self::CACHE_KEY_PREFIX);
    }
}

