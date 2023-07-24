<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderDetail>
 */
class OrderDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Select a random country from the countries table
        $country = \App\Models\Country::inRandomOrder()->first();

        // Select a random index from the states of the selected country
        $states = json_decode($country->states, true);
        $stateIndex = $this->faker->numberBetween(0, count($states) - 1);

        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'first_kana' => $this->faker->firstName,
            'last_kana' => $this->faker->lastName,
            'phone' => $this->faker->phoneNumber,
            'billing_address1' => $this->faker->streetAddress,
            'billing_address2' => $this->faker->secondaryAddress,
            'billing_city' => $this->faker->city,
            'billing_state' => $stateIndex,
            'billing_zipcode' => $this->faker->postcode,
            'billing_country_code' => $country->code,
            'shipping_address1' => $this->faker->streetAddress,
            'shipping_address2' => $this->faker->secondaryAddress,
            'shipping_city' => $this->faker->city,
            'shipping_state' => $stateIndex,
            'shipping_zipcode' => $this->faker->postcode,
            'shipping_country_code' => $country->code,
        ];
    }
}
