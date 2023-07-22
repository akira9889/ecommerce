<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(CountrySeeder::class);
        $this->call(ProductSeeder::class);

        $this->call(UserSeeder::class);
        $this->call(ProfileSeeder::class);
        $this->call(ProfileAddressSeeder::class);

        $this->call(OrderSeeder::class);
    }
}
