<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfileAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all profiles
        $profiles = \App\Models\Profile::all();

        // For each profile, create a shipping and a billing address
        foreach ($profiles as $profile) {
            \App\Models\ProfileAddress::factory()->create([
                'profile_id' => $profile->id,
            ]);

            \App\Models\ProfileAddress::factory()->billing()->create([
                'profile_id' => $profile->id,
            ]);
        }
    }
}
