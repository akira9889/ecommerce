<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Enums\ProfileType;
use App\Http\Controllers\Controller;
use App\Models\Api\User;
use App\Enums\UserStatus;
use App\Http\Resources\OrderListResource;
use App\Models\Api\Product;
use App\Models\Order;
use App\Traits\ReportTrait;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use ReportTrait;

    public function activeCustomers()
    {
        $user = User::where('status', UserStatus::Active->value);

        $fromToDate = $this->getFromToDate();

        if ($fromToDate) {
            list($fromDate, $toDate) = $fromToDate;
            $user->where('created_at', '>=', $fromDate)
                ->where('created_at', '<=', $toDate);
        }

        return $user->count();
    }

    public function activeProducts()
    {
        return Product::where('published', true)->count();
    }

    public function paidOrders()
    {
        $order = Order::where('status', '<>', OrderStatus::Canceled->value)
            ->where('status', '<>', OrderStatus::Unpaid->value);

        $fromToDate = $this->getFromToDate();

        if ($fromToDate) {
            list($fromDate, $toDate) = $fromToDate;
            $order->where('created_at', '>=', $fromDate)
                ->where('created_at', '<=', $toDate);
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
            $order->where('created_at', '>=', $fromDate)
                ->where('created_at', '<=', $toDate);
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
            $orders->where('orders.created_at', '>=', $fromDate)
                ->where('orders.created_at', '<=', $toDate);
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
}
