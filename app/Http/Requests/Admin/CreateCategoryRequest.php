<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:categories,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'image_url' => 'nullable|url',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Введите название категории',
            'name.unique' => 'Категория с таким названием уже существует',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Поддерживаемые форматы: jpeg, png, jpg, svg, webp',
            'image.max' => 'Размер изображения не должен превышать 2MB',
            'image_url.url' => 'Некорректный URL',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->hasFile('image') && !$this->filled('image_url')) {
                $validator->errors()->add('image', 'Необходимо загрузить изображение или указать URL');
            }
        });
    }
}