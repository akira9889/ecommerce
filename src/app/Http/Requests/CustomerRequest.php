<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;

class CustomerRequest extends FormRequest
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
            'status' => ['required', 'boolean'],

            'shippingAddress.address1' => ['required'],
            'shippingAddress.address2' => ['nullable'],
            'shippingAddress.city' => ['required'],
            'shippingAddress.state' => ['required'],
            'shippingAddress.zipcode' => ['required'],
            'shippingAddress.country_code' => ['required', 'exists:countries,code'],

            'billingAddress.address1' => ['required'],
            'billingAddress.address2' => ['nullable'],
            'billingAddress.city' => ['required'],
            'billingAddress.state' => ['required'],
            'billingAddress.zipcode' => ['required'],
            'billingAddress.country_code' => ['required', 'exists:countries,code'],
        ];
    }

    public function attributes()
    {
        return [
            'billingAddress.address1' => '丁目・番地・号',
            'billingAddress.address2' => '建物名',
            'billingAddress.city' => '市町村',
            'billingAddress.state' => '都道府県',
            'billingAddress.zipcode' => '郵便番号',
            'billingAddress.country_code' => '国•地域',

            'shippingAddress.address1' => '丁目・番地・号',
            'shippingAddress.address2' => '建物名',
            'shippingAddress.city' => '市町村',
            'shippingAddress.state' => '都道府県',
            'shippingAddress.zipcode' => '郵便番号',
            'shippingAddress.country_code' => '国•地域',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->messages();

        $billingErrors = [];
        $shippingErrors = [];
        $otherErrors = [];

        foreach ($errors as $field => $message) {
            if (Str::startsWith($field, 'billingAddress.')) {
                $billingErrors[Str::after($field, 'billingAddress.')] = $message;
            } elseif (Str::startsWith($field, 'shippingAddress.')) {
                $shippingErrors[Str::after($field, 'shippingAddress.')] = $message;
            } else {
                $otherErrors[$field] = $message;
            }
        }

        $transformed = array_merge($otherErrors, [
            'billingAddress' => $billingErrors,
            'shippingAddress' => $shippingErrors,
        ]);

        throw new HttpResponseException(response()->json($transformed, 422));
    }
}
