<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'status' => $this->status,
            'total_price' => $this->total_price,
            'items' => $this->items->map(fn($item) => [
                'id' => $item->id,
                'unit_price' => $item->unit_price,
                'quantity' => $item->quantity,
                'product' => [
                    'id' => $item->product->id,
                    'slug' => $item->product->slug,
                    'title' => $item->product->title,
                    'image' => $item->product->image,
                ],
            ]),
            'customer' => [
                'id' => $this->user->id,
                'email' => $this->user->email,
                'first_name' => $this->orderDetail->first_name,
                'last_name' => $this->orderDetail->last_name,
                'phone' => $this->orderDetail->phone,
                'shippingAddress' => [
                    'id' => $this->orderDetail->id,
                    'address1' => $this->orderDetail->shipping_address1,
                    'address2' => $this->orderDetail->shipping_address2,
                    'city' => $this->orderDetail->shipping_city,
                    'state' => json_decode($this->orderDetail->shippingCountry->states, true)[$this->orderDetail->shipping_state],
                    'zipcode' => $this->orderDetail->shipping_zipcode,
                    'country' => $this->orderDetail->shippingCountry->name,
                ],
                'billingAddress' => [
                    'id' => $this->orderDetail->id,
                    'address1' => $this->orderDetail->billing_address1,
                    'address2' => $this->orderDetail->billing_address2,
                    'city' => $this->orderDetail->billing_city,
                    'state' => json_decode($this->orderDetail->billingCountry->states, true)[$this->orderDetail->billing_state],
                    'zipcode' => $this->orderDetail->billing_zipcode,
                    'country' => $this->orderDetail->billingCountry->name,
                ],
            ],
            'created_at' => (new \DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
        ];
    }
}
