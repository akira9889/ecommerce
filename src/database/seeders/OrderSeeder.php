<?php

namespace Database\Seeders;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::all()->each(function (User $user) {
            $orders = Order::factory()
                ->count(5)
                ->create(['created_by' => $user->id]);

            foreach ($orders as $order) {
                $orderItems = OrderItem::factory()
                    ->count(5)
                    ->create(['order_id' => $order->id]);

                $total_price = $orderItems->reduce(function ($carry, $orderItem) {
                    return $carry + ($orderItem->unit_price * $orderItem->quantity);
                }, 0);

                $order->total_price = $total_price;
                $order->save();

                OrderDetail::factory()->create(['order_id' => $order->id]);

                Payment::factory()->create([
                    'order_id' => $order->id,
                    'amount' => $total_price,
                    'status' => PaymentStatus::Paid,
                    'created_by' => $user->id,
                    'updated_by' => $user->id
                ]);
            }
        });
    }
}
