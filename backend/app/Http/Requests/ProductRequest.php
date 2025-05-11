<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Определить, авторизован ли пользователь для выполнения запроса
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации для запроса
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'article' => 'required|string|max:50',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'specifications' => 'sometimes|array',
            'quantity_in_stock' => 'sometimes|integer|min:0',
        ];

        // Если это обновление, article может быть уникальным, исключая текущий ID
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['article'] = 'required|string|max:50|unique:products,article,' . $this->product;
        } else {
            $rules['article'] = 'required|string|max:50|unique:products,article';
        }

        return $rules;
    }
}
