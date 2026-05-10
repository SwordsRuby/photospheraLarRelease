<?php

namespace App\Http\Requests\Image;

use Illuminate\Foundation\Http\FormRequest;

class StoreImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array|max:8',
            'tags.*' => 'exists:tags,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:5120',
            'image_url' => 'nullable|url',
        ];
    }

    public function messages(): array
    {
        return [
            // name
            'name.required' => 'Название изображения обязательно для заполнения',
            'name.string' => 'Название изображения должно быть строкой',
            'name.max' => 'Название изображения не может превышать 50 символов',

            // category_id
            'category_id.required' => 'Выберите категорию',
            'category_id.exists' => 'Выбранная категория не существует',

            // tags
            'tags.array' => 'Теги должны быть переданы массивом',
            'tags.max' => 'Максимум 8 тегов',
            'tags.*.exists' => 'Один из выбранных тегов не существует',

            // image
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Изображение должно быть одного из форматов: jpeg, png, jpg, svg, webp',
            'image.max' => 'Размер изображения не должен превышать 5120 килобайт (5MB)',

            // image_url
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