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
            'name' => 'required|string|max:255',
            'max_transactions' => 'required|integer',
            'description' => 'nullable|string',
            'features' => 'required|array|min:1',
            'features.*' => 'exists:features,id',
            'periods' => 'required|array|min:1',
            'periods.*.id' => 'required|exists:periods,id',
            'periods.*.price_cents' => 'required|integer|min:0',
            'periods.*.discount_percentage' => 'nullable|integer|min:0|max:100',
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
