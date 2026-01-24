<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePreferenceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'items' => 'required|array',
            'items.*.title' => 'required|string',
            'items.*.unit_price' => 'required|numeric',
            'items.*.quantity' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'items.required' => 'Os itens são obrigatórios.',
            'items.array' => 'Os itens devem ser um array.',
            'items.*.title.required' => 'O título do item é obrigatório.',
            'items.*.unit_price.required' => 'O preço do item é obrigatório.',
            'items.*.quantity.required' => 'A quantidade do item é obrigatória.'
        ];
    }
}
