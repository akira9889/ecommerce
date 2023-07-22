<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'email' => 'admin@example.com',
                'password' => bcrypt('admin123'),
                'is_admin' => true
            ],
            [
                'email' => 'aki_badmin89@icloud.com',
                'password' => bcrypt('password'),
                'is_admin' => true
            ],
        ];

        foreach ($users as $userData) {
            User::factory()
            ->create($userData);
        }

        // Create 50 admin users
        \App\Models\User::factory(48)->create(['is_admin' => true]);

        // Create 50 non-admin users
        \App\Models\User::factory(50)->create(['is_admin' => false]);
    }
}
