<?php

namespace App\Http\Resources;

use App\Enums\UserStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
        $shipping = $this->profile->shippingAddress;
        $billing = $this->profile->billingAddress;

        $customerData = [
            'id' => $this->id,
            'first_name' => $this->profile->first_name,
            'last_name' => $this->profile->last_name,
            'first_kana' => $this->profile->first_kana,
            'last_kana' => $this->profile->last_kana,
            'email' => $this->email,
            'phone' => $this->profile->phone,
            'status' => $this->status === UserStatus::Active->value,
            'created_at' => (new \DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
        ];

            $customerData['shippingAddress'] = [
                'address1' => $shipping->address1 ?? '',
                'address2' => $shipping->address2 ?? '',
                'city' => $shipping->city ?? '',
                'state' => $shipping->state ?? '',
                'zipcode' => $shipping->zipcode ?? '',
                'country_code' => $shipping->country->code ?? '',
            ];

            $customerData['billingAddress'] = [
                'address1' => $billing->address1 ?? '',
                'address2' => $billing->address2 ?? '',
                'city' => $billing->city ?? '',
                'state' => $billing->state ?? '',
                'zipcode' => $billing->zipcode ?? '',
                'country_code' => $billing->country->code ?? '',
            ];

        return $customerData;
    }
}
