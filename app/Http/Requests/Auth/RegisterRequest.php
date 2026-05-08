<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'loginRegister' => 'required|string|max:20|unique:users,login',
            'emailRegister' => 'required|email|max:255|unique:users,email',
            'passwordRegister' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'loginRegister.required' => 'Логин обязателен',
            'loginRegister.max' => 'Логин не может превышать 20 символов',
            'loginRegister.unique' => 'Этот логин уже занят',
            'emailRegister.required' => 'Email обязателен',
            'emailRegister.email' => 'Введите корректный email',
            'emailRegister.unique' => 'Этот email уже зарегистрирован',
            'passwordRegister.required' => 'Пароль обязателен',
            'passwordRegister.min' => 'Пароль должен содержать минимум 8 символов',
            'passwordRegister.confirmed' => 'Пароли не совпадают',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        
        // Сохраняем ошибки в сессию и добавляем флаг для открытия модального окна
        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('showRegModal', true)  // КЛЮЧЕВОЙ ФЛАГ
        );
    }
}