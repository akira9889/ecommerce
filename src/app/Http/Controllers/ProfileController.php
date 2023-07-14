<?php

namespace App\Http\Controllers;

use App\Enums\AddressType;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\Country;
use App\Models\ProfileAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function view(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;
        $shippingAddress = $profile->shippingAddress ?: new ProfileAddress(['type' => AddressType::Shipping->value]);
        $billingAddress = $profile->billingAddress ?: new ProfileAddress(['type' => AddressType::Billing->value]);

        $countries = Country::query()->orderBy('name')->get();
        return view('profile.view', compact('profile', 'user', 'shippingAddress', 'billingAddress', 'countries'));
    }

    public function store(ProfileRequest $request)
    {
        $profileData = $request->validated();

        $shippingData = $profileData['shipping'];
        $billingData = $profileData['billing'];

        $user = $request->user();

        $profile = $user->profile;
        $profile->update($profileData);

        if ($profile->shippingAddress) {
            $profile->shippingAddress->update($shippingData);
        } else {
            $shippingData['profile_id'] = $profile->id;
            $shippingData['type'] = AddressType::Shipping->value;
            ProfileAddress::create($shippingData);
        }
        if ($profile->billingAddress) {
            $profile->billingAddress->update($billingData);
        } else {
            $billingData['profile_id'] = $profile->id;
            $billingData['type'] = AddressType::Billing->value;
            ProfileAddress::create($billingData);
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
