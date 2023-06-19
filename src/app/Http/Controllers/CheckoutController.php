<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Helpers\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        list($products, $cartItems) = Cart::getProductsAndCartItems();
        $lineItems = [];
        $totalPrice = 0;
        foreach ($products as $product) {
            $quantity = $cartItems[$product->id]['quantity'];
            $totalPrice += $product->price * $quantity;
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => $product->price,
                    'product_data' => [
                        'name' => $product->title,
                        'description' => $product->description,
                        'images' => [$product->image],
                    ],
                ],
                'quantity' => $quantity,
            ];
        }

        $user = $request->user();
        $firstName = $user->customer->first_name;
        $lastName = $user->customer->last_name;
        $name = $lastName . ' ' . $firstName;
        $email = $user->email;

        $existingCustomer = $stripe->customers->search([
            'query' => 'email:\'' . $email . '\'',
        ])->data[0] ?? null;

        $customerId = '';

        if ($existingCustomer) {
            $customerId = $existingCustomer['id'];
            if ($name !== $existingCustomer['name']) {
                $stripe->customers->update(
                    $customerId,
                    [
                        'name' => $name
                    ]
                );
            }
        }

        if (empty($existingCustomer)) {
            $customerId = $stripe->customers->create([
                'name' => $name,
                'email' => $user->email
            ])['id'];
        }

        $session = $stripe->checkout->sessions->create([
            'line_items' => $lineItems,
            'customer' => $customerId,
            'mode' => 'payment',
            'success_url' => route('checkout.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.failure', [], true),
        ]);

        $orderData = [
            'total_price' => $totalPrice,
            'status' => OrderStatus::Unpaid,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ];

        $order = Order::create($orderData);

        $paymentData = [
            'order_id' => $order->id,
            'amount' => $totalPrice,
            'status' => PaymentStatus::Pending,
            'type' => 'cc',
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'session_id' => $session->id
        ];

        Payment::create($paymentData);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $user = $request->user();
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        try {
            $session_id = $request->get('session_id');

            $session = $stripe->checkout->sessions->retrieve($session_id);
            if (!$session) {
                return view('checkout.failure', ['message' => 'セッションが無効です']);
            }

            $payment = Payment::query()->where(['session_id' => $session->id, 'status' => PaymentStatus::Pending])->first();
            if (!$payment) {
                return view('checkout.failure', ['message' => '支払いが完了していません']);
            }

            $payment->status = PaymentStatus::Paid;
            $payment->update();

            $order = $payment->order;
            $order->status = OrderStatus::Paid;
            $order->update();

            CartItem::where(['user_id' => $user->id])->delete();

            $customer = $stripe->customers->retrieve($session->customer);

            return view('checkout.success', compact('customer'));
        } catch (\Exception $e) {
            return view('checkout.failure');
        }
    }

    public function failure(Request $request)
    {
        return view('checkout.failure');
    }
}
