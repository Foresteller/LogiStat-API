<?php

namespace App\Contracts;

interface WarehouseCatalogInterface
{
    public function getCatalog(): array;
    public function updateStock(int $warehouseId, int $productId, int $newQuantity): void;
    public function clearCatalogCache(): void;
    public function hasCache(): bool;
}
