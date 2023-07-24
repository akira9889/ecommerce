<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Enums\ProfileType;
use App\Http\Controllers\Controller;
use App\Models\Api\User;
use Illuminate\Http\Request;
use App\Enums\UserStatus;
use App\Http\Resources\OrderListResource;
use App\Models\Api\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function activeCustomers()
    {
        return User::where('status', UserStatus::Active->value)->count();
    }

    public function activeProducts()
    {
        //TODO アクティブ商品だけを取得
        return Product::count();
    }

    public function paidOrders()
    {
        return Order::where('status', '<>', OrderStatus::Canceled->value)
            ->where('status', '<>', OrderStatus::Unpaid->value)
            ->count();
    }

    public function totalIncome()
    {
        return Order::where('status', '<>', OrderStatus::Canceled->value)
            ->where('status', '<>', OrderStatus::Unpaid->value)
            ->sum('total_price');
    }

    public function orderByCountry()
    {
        $orders = Order::query()
            ->select(['countries.name', DB::raw('count(orders.id) as count')])
            ->join('order_details', 'orders.id', '=', 'order_id')
            ->join('countries', 'order_details.billing_country_code', '=', 'code')
            ->where('orders.status', '<>', OrderStatus::Canceled->value)
            ->where('orders.status', '<>', OrderStatus::Unpaid->value)
            ->groupBy('billing_country_code')
            ->get();

        // $ordersArray = $orders->toArray();

        // Log::debug(json_encode($ordersArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $orders;
    }

    public function latestCustomers()
    {
        return User::query()
            ->select('users.id', 'profiles.first_name', 'profiles.last_name', 'users.email')
            ->join('profiles', 'users.id', '=', 'user_id')
            ->where('users.status', UserStatus::Active->value)
            ->where('profiles.type', ProfileType::Customer->value)
            ->limit(5)
            ->orderBy('users.created_at', 'desc')
            ->get();
    }

    public function latestOrders()
    {
        $orders = Order::with('items')
            ->where('orders.status', '<>', OrderStatus::Canceled->value)
            ->where('orders.status', '<>', OrderStatus::Unpaid->value)
            ->limit(10)
            ->orderBy('orders.created_at', 'desc')
            ->get();

        return OrderListResource::collection($orders);
    }
}
