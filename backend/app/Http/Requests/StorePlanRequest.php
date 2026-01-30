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
            'price_cents' => 'required|integer|min:1',
            'interval' => 'required|string',
            'max_transactions' => 'required|integer',
            'description' => 'nullable|string',
            'features' => 'required|array|min:1',
            'is_active' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'price_cents.required' => 'O preço é obrigatório.',
            'price_cents.integer' => 'O preço deve ser um número inteiro (centavos).',
            'price_cents.min' => 'O preço deve ser maior que zero.',
            'interval.required' => 'O intervalo é obrigatório.',
            'max_transactions.required' => 'O número máximo de transações é obrigatório.',
            'features.required' => 'Pelo menos uma função deve ser informada.',
            'features.min' => 'É necessário informar pelo menos uma função.'
        ];
    }
}
