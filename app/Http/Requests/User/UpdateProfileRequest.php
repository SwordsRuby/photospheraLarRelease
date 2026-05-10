<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'login' => 'required|string|max:50|unique:users,login,' . $userId,
            'password' => 'nullable|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            // login
            'login.required' => 'Введите логин',
            'login.string' => 'Логин должен быть строкой',
            'login.max' => 'Логин не может превышать 50 символов',
            'login.unique' => 'Пользователь с таким логином уже существует',

            // password
            'password.string' => 'Пароль должен быть строкой',
            'password.min' => 'Пароль должен содержать минимум 8 символов',
            'password.confirmed' => 'Пароли не совпадают',

            // image
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Изображение должно быть одного из форматов: jpeg, png, jpg, svg, webp',
            'image.max' => 'Размер изображения не должен превышать 2MB',
        ];
    }
}