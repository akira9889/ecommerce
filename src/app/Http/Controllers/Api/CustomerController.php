<?php

namespace App\Http\Controllers\Api;

use App\Enums\AddressType;
use App\Enums\CustomerStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CountryResource;
use App\Http\Resources\CustomerListResource;
use App\Http\Resources\CustomerResource;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CustomerAddress;

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

        $customers = Customer::query()->leftJoin('users', 'customers.user_id', '=', 'users.id');

        if ($search) {
            $customers->where('users.email', $search)
                ->orWhere('customers.phone', $search)
                ->orWhereRaw("CONCAT(customers.last_name, customers.first_name ) LIKE ?", ["%{$search}%"]);
        }

        if ($sortField === 'name') {
            $customers = $customers->orderByRaw("CONCAT(last_name, first_name) {$sortDirection}");
        } elseif ($sortField === 'email') {
            $customers->orderBy("users.$sortField", $sortDirection);
        } else {
            $customers = $customers->orderBy("customers.$sortField", $sortDirection);
        }

        $customers = $customers->paginate($perPage);

        return CustomerListResource::collection($customers);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CustomerRequest  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        $customerData = $request->validated();
        $customerData['updated_by'] = $request->user()->id;
        $customerData['status'] = $customerData['status'] ? CustomerStatus::Active->value : CustomerStatus::Disabled->value;
        $shippingData = $customerData['shippingAddress'];
        $billingData = $customerData['billingAddress'];

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

        return new CustomerResource($customer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->noContent();
    }

    public function countries()
    {
        return CountryResource::collection(Country::query()->orderBy('code', 'asc')->get());
    }
}
