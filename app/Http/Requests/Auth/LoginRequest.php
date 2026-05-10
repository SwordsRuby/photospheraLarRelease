<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'loginAuth' => 'required|string',
            'passwordAuth' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            // loginAuth
            'loginAuth.required' => 'Введите логин',

            // passwordAuth
            'passwordAuth.required' => 'Введите пароль',
        ];
    }
}