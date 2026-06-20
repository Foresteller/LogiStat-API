<?php

namespace Database\Factories;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Warehouse>
 */
class WarehouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $warehouses = ['Центральный склад', 'Склад Север', 'Склад Юг', 'Логистический склад', 'Склад Сибирь'];
        return [
            'name' => $this->faker->randomElement($warehouses) . ' ' . $this->faker->buildingNumber(),
            'city' => $this->faker->city(),
            'address' => $this->faker->address(),
        ];
    }
}
