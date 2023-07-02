<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Customer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    public function withCustomer()
    {
        return $this->afterCreating(function (User $user) {
            $names = explode(" ", $user->name);

            $katakanaLastName = $this->convertToKatakana($names[0]);
            $katakanaFirstName = !empty($names[1]) ? $this->convertToKatakana($names[1]) : '';

            $customer = new Customer();
            $customer->user_id = $user->id;
            $customer->last_name = $katakanaLastName;
            $customer->first_name = $katakanaFirstName;
            $customer->save();
        });
    }

    private function convertToKatakana($text)
    {
        // goo APIのエンドポイント
        $url = 'https://labs.goo.ne.jp/api/hiragana';

        // リクエストパラメータ
        $data = [
            'app_id' => env('GOO_APP_ID'), // app_id
            'sentence' => $text, // 変換したい文章
            'output_type' => 'katakana' // 出力タイプ
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);

        curl_close($ch);

        $response = json_decode($response, true);

        if (!is_array($response) || !isset($response['converted'])) {
            throw new \Exception('Failed to convert name to Katakana using goo API');
        }

        return $response['converted'];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
