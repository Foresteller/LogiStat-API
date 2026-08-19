<?php

namespace App\Providers;

use App\Contracts\WarehouseCatalogInterface;
use App\Http\Services\WarehouseService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(WarehouseCatalogInterface::class, WarehouseService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
