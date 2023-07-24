<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProfileAddress>
 */
class ProfileAddressFactory extends Factory
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
            'address1' => $this->faker->streetAddress,
            'address2' => $this->faker->secondaryAddress,
            'city' => $this->faker->city,
            'state' => $stateIndex,
            'zipcode' => $this->faker->postcode,
            'country_code' => $country->code,
            'type' => 'shipping',  // Default to shipping
        ];
    }

    public function billing()
    {
        return $this->state(['type' => 'billing']);
    }
}
