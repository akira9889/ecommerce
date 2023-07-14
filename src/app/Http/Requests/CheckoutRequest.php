<?php

namespace App\Http\Requests;

use App\Models\CustomerAddress;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    protected $redirectRoute = 'cart.index';
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = $this->user();
        $user = $this->user();
        $hasShipping = CustomerAddress::where('customer_id', $user->id)
            ->where('type', 'shipping')
            ->exists();

        $hasBilling = CustomerAddress::where('customer_id', $user->id)
            ->where('type', 'billing')
            ->exists();
        return $hasShipping && $hasBilling;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
