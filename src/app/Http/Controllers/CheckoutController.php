<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Helpers\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckoutController extends Controller
{
    private $stripe;

    public function __construct()
    {
        $this->stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));
    }

    public function checkout(Request $request)
    {
        list($products, $cartItems) = Cart::getProductsAndCartItems();
        $orderItems = [];
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

            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price
            ];
        }

        $customerId = $this->getOrCreateCustomer($request);

        $session = $this->stripe->checkout->sessions->create([
            'line_items' => $lineItems,
            'customer' => $customerId,
            'mode' => 'payment',
            'success_url' => route('checkout.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.failure', [], true),
        ]);

        $user = $request->user();

        // Create Order
        $orderData = [
            'total_price' => $totalPrice,
            'status' => OrderStatus::Unpaid,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ];

        $order = Order::create($orderData);

        //Create Order Items
        foreach ($orderItems as $orderItem) {
            $orderItem['order_id'] = $order->id;
            OrderItem::create($orderItem);
        }

        // Create Payment
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

        CartItem::where(['user_id' => $user->id])->delete();

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $user = $request->user();

        try {
            $session_id = $request->get('session_id');

            $session = $this->stripe->checkout->sessions->retrieve($session_id);
            if (!$session) {
                return view('checkout.failure', ['message' => 'セッションが無効です']);
            }

            $payment = Payment::query()
            ->where(['session_id' => $session_id])
            ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::Paid])
            ->first();
            if (!$payment) {
                throw new NotFoundHttpException();
            }
            if ($payment->status === PaymentStatus::Pending->value) {
                $this->updateOrderAndSession($payment);
            }

            $customer = $this->stripe->customers->retrieve($session->customer);

            return view('checkout.success', compact('customer'));
        } catch (\Exception $e) {
            return view('checkout.failure');
        }
    }

    public function failure(Request $request)
    {
        return view('checkout.failure');
    }

    public function checkoutOrder(Order $order, Request $request)
    {
        $lineItems = [];
        foreach ($order->items as $item) {

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => $item->unit_price,
                    'product_data' => [
                        'name' => $item->product->title,
                        'description' => $item->product->description,
                        'images' => [$item->product->image],
                    ],
                ],
                'quantity' => $item->quantity,
            ];
        }

        $customerId = $this->getOrCreateCustomer($request);

        $session = $this->stripe->checkout->sessions->create([
            'line_items' => $lineItems,
            'customer' => $customerId,
            'mode' => 'payment',
            'success_url' => route('checkout.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.failure', [], true),
        ]);

        $order->payment->session_id = $session->id;
        $order->payment->save();

        return redirect($session->url);
    }

    private function updateOrderAndSession(Payment $payment)
    {
        $payment->status = PaymentStatus::Paid;
        $payment->update();

        $order = $payment->order;
        $order->status = OrderStatus::Paid;
        $order->update();
    }

    public function getOrCreateCustomer(Request $request)
    {
        $user = $request->user();
        $firstName = $user->customer->first_name;
        $lastName = $user->customer->last_name;
        $name = $lastName . ' ' . $firstName;
        $email = $user->email;

        $existingCustomerId = $this->searchExistingCustomer($email);

        if ($existingCustomerId) {
            $customer = $this->stripe->customers->retrieve($existingCustomerId);
            if ($name !== $customer['name']) {
                return $this->updateCustomerName($existingCustomerId, $name);
            }

            return $existingCustomerId;
        }

        return $this->createNewCustomer($name, $email);
    }

    private function searchExistingCustomer($email)
    {
        $existingCustomer = $this->stripe->customers->search([
            'query' => 'email:\'' . $email . '\'',
        ])->data[0] ?? null;

        if ($existingCustomer) {
            return $existingCustomer['id'];
        }

        return null;
    }

    private function updateCustomerName($customerId, $name)
    {
        $this->stripe->customers->update(
            $customerId,
            [
                'name' => $name
            ]
        );

        return $customerId;
    }

    private function createNewCustomer($name, $email)
    {
        $newCustomer = $this->stripe->customers->create([
            'name' => $name,
            'email' => $email
        ]);

        return $newCustomer['id'];
    }


}
