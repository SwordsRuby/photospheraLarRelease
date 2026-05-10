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
            // name
            'name.required' => 'Поле "Название категории" обязательно для заполнения',
            'name.string' => 'Поле "Название категории" должно быть строкой',
            'name.max' => 'Поле "Название категории" не может превышать 100 символов',
            'name.unique' => 'Категория с таким названием уже существует',

            // image
            'image.required' => 'Поле "Изображение" обязательно для заполнения',
            'image.image' => 'Файл в поле "Изображение" должен быть изображением',
            'image.mimes' => 'Поле "Изображение" должно быть файлом одного из типов: jpeg, png, jpg, svg, webp',
            'image.max' => 'Поле "Изображение" не должно превышать 2048 килобайт (2MB)',

            // image_url
            'image_url.required' => 'Поле "URL изображения" обязательно для заполнения',
            'image_url.url' => 'Поле "URL изображения" должно содержать корректный URL-адрес',
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