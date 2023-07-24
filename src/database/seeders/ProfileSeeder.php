<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = \App\Models\User::all();

        foreach ($users as $user) {
            $newProfile = \App\Models\Profile::factory()->create(['user_id' => $user->id]);

            if ($user->is_admin) {
                \App\Models\Profile::factory()->admin()->create([
                    'user_id' => $user->id,
                    'first_name' => $newProfile['first_name'],
                    'last_name' => $newProfile['last_name'],
                    'first_kana' => $newProfile['first_kana'],
                    'last_kana' => $newProfile['last_kana'],
                ]);
            }
        }
    }
}
