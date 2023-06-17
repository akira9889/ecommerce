<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'phone' => ['required', 'min:7'],
            'email' => ['required', 'email'],

            'shipping.address1' => ['required'],
            'shipping.address2' => ['nullable'],
            'shipping.city' => ['required'],
            'shipping.state' => ['required'],
            'shipping.zipcode' => ['required'],
            'shipping.country_code' => ['required', 'exists:countries,code'],

            'billing.address1' => ['required'],
            'billing.address2' => ['nullable'],
            'billing.city' => ['required'],
            'billing.state' => ['required'],
            'billing.zipcode' => ['required'],
            'billing.country_code' => ['required', 'exists:countries,code'],
        ];
    }

    public function attributes()
    {
        return [
            'billing.address1' => '丁目・番地・号',
            'billing.address2' => '建物名',
            'billing.city' => '市町村',
            'billing.state' => '都道府県',
            'billing.zipcode' => '郵便番号',
            'billing.country_code' => '国',
            'shipping.address1' => '丁目・番地・号',
            'shipping.address2' => '建物名',
            'shipping.zipcode' => '郵便番号',
            'shipping.country_code' => '国',
        ];
    }
}
