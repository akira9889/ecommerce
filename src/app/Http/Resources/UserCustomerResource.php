<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCustomerResource extends JsonResource
{

    public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'first_name' => $this->customer->first_name,
            'last_name' => $this->customer->last_name,
            'phone' => $this->customer->phone,
            'shippingAddress' => new CustomerAddressResource($this->customer->shippingAddress),
            'billingAddress' => new CustomerAddressResource($this->customer->billingAddress),
        ];
    }
}
