<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateModeratorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => 'required|string|max:50|unique:users,login',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            // login
            'login.required' => 'Логин обязательно для заполнения',
            'login.string' => 'Логин должен быть строкой',
            'login.max' => 'Логин не может превышать 50 символов',
            'login.unique' => 'Пользователь с таким логином уже существует',

            // password
            'password.required' => 'Пароль обязательно для заполнения',
            'password.string' => 'Пароль должен быть строкой',
            'password.min' => 'Пароль должен содержать минимум 8 символов',
            'password.confirmed' => 'Пароли не совпадают',
        ];
    }
}