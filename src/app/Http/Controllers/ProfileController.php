<?php

namespace App\Http\Controllers;

use App\Enums\AddressType;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\Country;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function view(Request $request)
    {
        $user = $request->user();
        $customer = $user->customer;
        $shippingAddress = $customer->shippingAddress ?: new CustomerAddress(['type' => AddressType::Shipping->value]);
        $billingAddress = $customer->billingAddress ?: new CustomerAddress(['type' => AddressType::Billing->value]);

        $countries = Country::query()->orderBy('name')->get();
        return view('profile.view', compact('customer', 'user', 'shippingAddress', 'billingAddress', 'countries'));
    }

    public function store(ProfileRequest $request)
    {
        $customerData = $request->validated();

        $shippingData = $customerData['shipping'];
        $billingData = $customerData['billing'];

        $user = $request->user();

        $customer = $user->customer;
        $customer->update($customerData);

        if ($customer->shippingAddress) {
            $customer->shippingAddress->update($shippingData);
        } else {
            $shippingData['customer_id'] = $customer->user_id;
            $shippingData['type'] = AddressType::Shipping->value;
            CustomerAddress::create($shippingData);
        }
        if ($customer->billingAddress) {
            $customer->billingAddress->update($billingData);
        } else {
            $billingData['customer_id'] = $customer->user_id;
            $billingData['type'] = AddressType::Billing->value;
            CustomerAddress::create($billingData);
        }

        $request->session()->flash('flash_message', 'プロフィールを更新しました。');

        return to_route('profile');
    }

    public function passwordUpdate(PasswordUpdateRequest $request)
    {
        $user = $request->user();

        $passwordData = $request->validated();

        $user->password = Hash::make($passwordData['new_password']);
        $user->save();

        $request->session()->flash('flash_message', 'パスワードを更新しました。');

        return to_route('profile');
    }
}
