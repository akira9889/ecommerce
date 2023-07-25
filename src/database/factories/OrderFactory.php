<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'total_price' => 0,
            'status' => $this->faker->randomElement(array_keys(OrderStatus::getStatuses())),
            'created_at' => fake()->dateTimeBetween('-2 year', 'now'),
        ];
    }
}
