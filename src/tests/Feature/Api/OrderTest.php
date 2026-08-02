<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Jobs\ProcessOrderJob;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест: Гость(не аутентифицированный) получает 401 Unauthorized
     */
    public function test_guest_cannot_create_order(): void
    {
        $response = $this->postJson('/api/orders', []);
        $response->assertStatus(401)->assertJson([
            'success' => false,
            'message' => 'Unauthenticated'
        ]);
    }

    /**
     * Тест: Успешное создание заказа клиентом + пуш в очередь
     */
    public function test_client_can_create_order_successfully(): void
    {
        Queue::fake();

        $user = User::factory()->create(['role' => UserRole::CLIENT]);
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create(['price' => 1000.00]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/orders', [
            'user_id' => $user->id,
            'warehouse_id' => $warehouse->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'count' => 8
                ]
            ]
        ]);

        $response->assertStatus(201)->assertJsonStructure([
            'message',
            'order_id',
            'status',
            'total_amount'
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'warehouse_id' => $warehouse->id,
            'status' => 'pending',
            'total_amount' => 8000.00
        ]);

        Queue::assertPushed(ProcessOrderJob::class);
    }

    /**
     * Тест: Проверка ролевого доступа
     */
    public function test_user_with_invalid_role_cannot_create_order()
    {
        $user = User::factory()->create(['role' => UserRole::MANAGER]);
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/orders',[]);
        $response->assertStatus(403)->assertJson([
            'success' => false,
            'message' => 'Forbidden. Not enough permissions'
        ]);
    }
}
