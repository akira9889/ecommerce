<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'first_kana' => $this->faker->firstKanaName,
            'last_kana' => $this->faker->lastKanaName,
            'phone' => str_replace('-', '', $this->faker->phoneNumber),
            'type' => 'customer',
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now')
        ];
    }

    public function admin()
    {
        return $this->state(['type' => 'admin']);
    }
}
