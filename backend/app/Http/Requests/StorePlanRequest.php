<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'price' => 'required|numeric|min:0.01',
            'interval' => 'required|string',
            'max_transactions' => 'required|integer',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'is_active' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'price.required' => 'O preço é obrigatório.',
            'price.numeric' => 'O preço deve ser numérico.',
            'price.min' => 'O preço deve ser maior que zero.',
            'interval.required' => 'O intervalo é obrigatório.',
            'max_transactions.required' => 'O número máximo de transações é obrigatório.'
        ];
    }
}
