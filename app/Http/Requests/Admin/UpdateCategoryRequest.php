<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('id');

        return [
            'name' => 'required|string|max:100|unique:categories,name,' . $categoryId,
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Название категории обязательно для заполнения',
            'name.string' => 'Название категории должно быть строкой',
            'name.max' => 'Название категории не может превышать 100 символов',
            'name.unique' => 'Категория с таким названием уже существует',
        ];
    }
}