<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'email_verified_at' => now(),
                'password' => bcrypt('admin123'),
                'is_admin' => true
            ],
            [
                'name' => '岩澤 明',
                'email' => 'aki_badmin89@icloud.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        ];
        foreach ($users as $user) {
            $user = User::create($user);
            $customer = new Customer();
            $names = explode(" ", $user->name);
            $customer->user_id = $user->id;
            $customer->last_name = $names[0];
            $customer->first_name = $names[1] ?? '';
            $customer->save();
        }
    }
}
