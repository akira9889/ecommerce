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
        return view('order.view', compact('order'));
    }

    public function cancel(Request $request, Order $order)
    {
        $order->status = OrderStatus::Canceled->value;
        $order->save();

        $request->session()->flash('flash_message', '注文をキャンセルしました。');

        return to_route('order.view', compact('order'));
    }
}
