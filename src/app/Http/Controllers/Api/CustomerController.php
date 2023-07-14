<?php

namespace App\Http\Controllers\Api;

use App\Enums\AddressType;
use App\Enums\ProfileType;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CountryResource;
use App\Http\Resources\CustomerListResource;
use App\Http\Resources\CustomerResource;
use App\Models\Country;
use App\Models\Api\User;
use App\Models\ProfileAddress;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $search = request('search', false);
        $perPage = request('per_page', 10);
        $sortField = request('sort_field', 'updated_at');
        $sortDirection = request('sort_direction', 'desc');

        $customers = User::query()
            ->join('profiles', 'profiles.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.email',
                'users.status',
                "profiles.first_name",
                "profiles.last_name",
                "profiles.first_kana",
                "profiles.last_kana",
                "profiles.phone",
                "profiles.updated_at",
            )
            ->where('profiles.type', ProfileType::Customer);

        if ($search) {
            $customers->where(function ($query) use ($search) {
                $query->where('users.email', 'like', "%{$search}%")
                    ->orWhere('profiles.phone', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(profiles.last_kana, profiles.first_kana) LIKE ?", ["%{$search}%"]);
            });
        }

        if ($sortField === 'name') {
            $customers = $customers->orderByRaw("CONCAT(profiles.last_kana, profiles.first_kana) {$sortDirection}");
        } elseif ($sortField === 'email') {
            $customers->orderBy("users.$sortField", $sortDirection);
        } else {
            $customers = $customers->orderBy("profiles.$sortField", $sortDirection);
        }

        $customers = $customers->paginate($perPage);

        return CustomerListResource::collection($customers);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return new CustomerResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CustomerRequest  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, $id)
    {
        $customerData = $request->validated();

        $customerData['updated_by'] = $request->user()->id;
        $customerData['status'] = $customerData['status'] ? UserStatus::Active->value : UserStatus::Disabled->value;

        $shippingData = $customerData['shippingAddress'];
        $billingData = $customerData['billingAddress'];

        $customer = User::findOrFail($id);

        $customer->status = $customerData['status'];
        $customer->email = $customerData['email'];
        unset($customerData['status']);
        $customer->save();

        $customerProfile = $customer->profile;
        $customerProfile->update($customerData);

        if ($customerProfile->shippingAddress) {
            $customerProfile->shippingAddress->update($shippingData);
        } else {
            $shippingData['profile_id'] = $customerProfile->id;
            $shippingData['type'] = AddressType::Shipping->value;
            ProfileAddress::create($shippingData);
        }
        if ($customerProfile->billingAddress) {
            $customerProfile->billingAddress->update($billingData);
        } else {
            $billingData['profile_id'] = $customerProfile->id;
            $billingData['type'] = AddressType::Billing->value;
            ProfileAddress::create($billingData);
        }

        return new CustomerResource($customer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = User::findOrFail($id);
        $customer->delete();

        return response()->noContent();
    }

    public function countries()
    {
        return CountryResource::collection(Country::query()->orderBy('code', 'asc')->get());
    }
}
