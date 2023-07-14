<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class CreateUserRequest extends FormRequest
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
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->numbers()->letters()->symbols()]
        ];
    }

    public function messages()
    {
        return [
            'first_kana.regex' => 'カタカナで入力してください',
            'last_kana.regex' => 'カタカナで入力してください',
        ];
    }
}
