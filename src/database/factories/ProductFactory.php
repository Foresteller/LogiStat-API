<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'sku' => strtoupper($this->faker->unique()->bothify('PROD-#####')),
            'name' => $this->faker->words(3, true),
            'price' => $this->faker->randomFloat(2, 50, 5000),
            'description' => $this->faker->sentence(),
        ];
    }
}
