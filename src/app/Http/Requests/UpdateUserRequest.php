<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
            'first_name' => ['required', 'max:55'],
            'last_name' => ['required', 'max:55'],
            'first_kana' => ['required', 'max:55', 'regex:/\A[ァ-ヶー]+\z/u'],
            'last_kana' => ['required', 'max:55', 'regex:/\A[ァ-ヶー]+\z/u'],
            'email' => ['required', 'email'],
            'password' => ['nullable', 'confirmed', Password::min(8)->numbers()->letters()->symbols()],
        ];
    }
}
