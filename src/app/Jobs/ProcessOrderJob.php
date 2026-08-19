<?php

namespace App\Jobs;

use App\Contracts\WarehouseCatalogInterface;
use App\Models\Order;
use App\Models\Stock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Order $order,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WarehouseCatalogInterface $service): void
    {
        Log::info("Началась обработка заказа №{$this->order->id} в очереди");
        $this->order->load('items');
        try {
            DB::transaction(function () {
                foreach ($this->order->items as $item) {
                    $stock = Stock::where(
                        'warehouse_id',
                        $this->order->warehouse_id
                    )
                        ->where('product_id', $item->product_id)
                        ->lockForUpdate()
                        ->first();

                    if (! $stock || $stock->quantity < $item->count) {
                        throw new \Exception(
                            "Недостаточно товара с ID {$item->product_id} на складе."
                        );
                    }
                    $stock->decrement('quantity', $item->count);
                }
                $this->order->update(['status' => 'processing']);
            });
            $service->clearCatalogCache();
            Log::info(
                "Заказ №{$this->order->id} успешно обработан и переведен в статус processing"
            );
        } catch (\Exception $exception) {
            $this->order->update(['status' => 'cancelled']);
            Log::warning(
                "Заказ №{$this->order->id} отменен: ".$exception->getMessage()
            );
        }
    }
}
