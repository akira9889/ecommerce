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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function activeCustomers()
    {
        $user = User::where('status', UserStatus::Active->value);

        $fromToDate = $this->getFromToDate();

        if ($fromToDate) {
            list($fromDate, $toDate) = $fromToDate;
            $user->where('created_at', '>=', $fromDate);
            $user->where('created_at', '<=', $toDate);
        }

        return $user->count();
    }

    public function activeProducts()
    {
        //TODO アクティブ商品だけを取得
        return Product::count();
    }

    public function paidOrders()
    {
        $order = Order::where('status', '<>', OrderStatus::Canceled->value)
            ->where('status', '<>', OrderStatus::Unpaid->value);

        $fromToDate = $this->getFromToDate();

        if ($fromToDate) {
            list($fromDate, $toDate) = $fromToDate;
            $order->where('created_at', '>=', $fromDate);
            $order->where('created_at', '<=', $toDate);
        }

            return $order->count();
    }

    public function totalIncome()
    {
        $order = Order::where('status', '<>', OrderStatus::Canceled->value)
        ->where('status', '<>', OrderStatus::Unpaid->value);

        $fromToDate = $this->getFromToDate();

        if ($fromToDate) {
            list($fromDate, $toDate) = $fromToDate;
            $order->where('created_at', '>=', $fromDate);
            $order->where('created_at', '<=', $toDate);
        }

        return $order->sum('total_price');
    }

    public function orderByCountry()
    {
        $orders = Order::query()
            ->select(['countries.name', DB::raw('count(orders.id) as count')])
            ->join('order_details', 'orders.id', '=', 'order_id')
            ->join('countries', 'order_details.billing_country_code', '=', 'code')
            ->where('orders.status', '<>', OrderStatus::Canceled->value)
            ->where('orders.status', '<>', OrderStatus::Unpaid->value)
            ->groupBy('billing_country_code');

        $fromToDate = $this->getFromToDate();

        if ($fromToDate) {
            list($fromDate, $toDate) = $fromToDate;
            $orders->where('orders.created_at', '>=', $fromDate);
            $orders->where('orders.created_at', '<=', $toDate);
        }

        return $orders->get();
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

    private function getFromToDate()
    {
        $d = \request()->get('d');
        $array = [
            'today' => [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()],
            '1d' => [Carbon::yesterday()->startOfDay()->toDateTimeString(), Carbon::yesterday()->endOfDay()->toDateTimeString()],
            '1w' => [Carbon::now()->subDays(7), Carbon::now()],
            '2w' => [Carbon::now()->subDays(14), Carbon::now()],
            '1m' => [Carbon::now()->subDays(30), Carbon::now()],
            '3m' => [Carbon::now()->subDays(60), Carbon::now()],
            '6m' => [Carbon::now()->subDays(180), Carbon::now()],
            '1y' => [Carbon::now()->subDays(365), Carbon::now()],
        ];

        return $array[$d] ?? null;
    }
}
