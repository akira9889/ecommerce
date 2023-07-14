<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;

use App\Http\Resources\OrderListResource;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;

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
        return new OrderResource($order);
    }

    public function getStatuses()
    {
        return OrderStatus::getStatuses();
    }

    public function changeStatus(Order $order, $status)
    {
        $order->status = $status;
        $order->save();

        return response('', 200);
    }
}
