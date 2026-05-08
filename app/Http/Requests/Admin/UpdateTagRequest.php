<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tagId = $this->route('id');

        return [
            'title' => 'required|string|max:100|unique:tags,title,' . $tagId,
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Введите название тега',
            'title.unique' => 'Тег с таким названием уже существует',
        ];
    }
}