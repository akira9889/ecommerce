<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Http\Resources\OrderListResource;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
    public function index()
    {
        $search = request('search', false);
        $perPage = request('per_page', 10);
        $sortField = request('sort_field', 'updated_at');
        $sortDirection = request('sort_direction', 'desc');

        $orders = Order::query();

        if ($search) {
            $orders->where('id', $search);
        }

        // if ($sortField === 'name') {
        //     $orders->orderByRaw("CONCAT(last_name, first_name) {$sortDirection}");
        // } else {
        $orders = $orders->orderBy($sortField, $sortDirection);
        // }

        return OrderListResource::collection($orders->paginate($perPage));
    }

    public function view(Order $order)
    {
        $order->load([
            'items.product' => function ($query) {
                $query->withTrashed();
            },
            'orderDetail',
            'orderDetail.shippingCountry',
            'orderDetail.billingCountry'
        ]);

        return new OrderResource($order);
    }

    public function getStatuses()
    {
        return OrderStatus::getStatuses();
    }

    public function changeStatus(Order $order, $status)
    {
        if ($status === OrderStatus::Canceled->value) {
            $this->cancel($order);
        } else {
            $order->status = $status;
            $order->save();

            return response('', 200);
        }
    }

    private function cancel(Order $order)
    {
        try {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

            $session_id = $order->payment->session_id;

            $session = $stripe->checkout->sessions->retrieve($session_id);
            if (!$session) {
                throw new \Exception('セッションが無効です');
            }

            $paymentIntent = $session->payment_intent;

            $refund = $stripe->refunds->create(['payment_intent' => $paymentIntent]);

            if ($refund->status === 'succeeded') {
                $order->status = OrderStatus::Canceled->value;
                $order->save();
                return response('', 200);
            } else {
                throw new \Exception('返金の処理に失敗しました。');
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }
}
