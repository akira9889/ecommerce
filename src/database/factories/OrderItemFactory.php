<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $product = Product::query()->inRandomOrder()->first();
        return [
            'product_id' => $product->id,
            'quantity' => $this->faker->numberBetween(1, 5),
            'unit_price' => $product->price
        ];
    }
}
