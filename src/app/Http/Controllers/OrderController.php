<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request) {
        $orders  = Order::query()
        ->where(['created_by' => $request->user()->id])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('order.index', compact('orders'));
    }

    public function view(Order $order)
    {
        $user = request()->user();
        if ($order->created_by !== $user->id) {
            return response('この注文の閲覧権限がありません', 403);
        }

        $order->load([
            'items.product' => function ($query) {
                $query->withTrashed();
            },
        ]);
        
        return view('order.view', compact('order'));
    }

    public function cancel(Request $request, Order $order)
    {
        try {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

            $session_id = $order->payment->session_id;


            $session = $stripe->checkout->sessions->retrieve($session_id);
            if (!$session) {
                return view('checkout.failure', ['message' => 'セッションが無効です']);
            }

            $paymentIntent = $session->payment_intent;

            $refund = $stripe->refunds->create(['payment_intent' => $paymentIntent]);

            if ($refund->status == 'succeeded') {
                $order->status = OrderStatus::Canceled->value;
                $order->save();

                $request->session()->flash('flash_message', '注文をキャンセルしました。');

                return to_route('order.view', compact('order'));
            } else {
                return view('checkout.failure', ['message' => '返金の処理に失敗しました。']);
            }

        } catch (\Exception $e) {
            return view('checkout.failure', ['message' => '返金の処理に失敗しました。']);
        }
    }
}
