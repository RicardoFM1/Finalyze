<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'string|max:255',
            'max_transactions' => 'integer',
            'description' => 'nullable|string',
            'features' => 'array|min:1',
            'features.*' => 'exists:features,id',
            'periods' => 'array|min:1',
            'periods.*.id' => 'required|exists:periods,id',
            'periods.*.price_cents' => 'required|integer|min:0',
            'periods.*.discount_percentage' => 'nullable|integer|min:0|max:100',
            'is_active' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'O nome deve ser uma string.',
            'price_cents.integer' => 'O preço deve ser um número inteiro (centavos).',
            'interval.string' => 'O intervalo deve ser uma string.',
            'max_transactions.integer' => 'O número máximo de transações deve ser um inteiro.',
            'features.min' => 'É necessário informar pelo menos uma função.'
        ];
    }
}
