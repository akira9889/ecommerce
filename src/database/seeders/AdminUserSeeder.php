<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
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
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('admin123'),
                'is_admin' => true
            ],
            [
                'name' => '岩澤 明',
                'email' => 'aki_badmin89@icloud.com',
                'password' => bcrypt('password'),
            ],
        ];

        foreach ($users as $userData) {
            User::factory()
            ->withCustomer()
            ->create($userData);
        }

        // ランダムなユーザーを10人作成
        User::factory()
        ->withCustomer()
        ->count(100)
        ->create();
    }
}
