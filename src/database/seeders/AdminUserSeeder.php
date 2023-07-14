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
            ->withProfile()
            ->create($userData);
        }

        // ランダムなユーザーを10人作成
        User::factory()
        ->withProfile()
        ->count(100)
        ->create();
    }
}
