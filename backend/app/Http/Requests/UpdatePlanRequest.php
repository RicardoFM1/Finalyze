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
            'name' => 'string',
                'price_cents' => 'integer',
            'interval' => 'string',
            'max_transactions' => 'integer',
            'description' => 'string',
            'features' => 'array',
            'is_active' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'O nome deve ser uma string.',
                'price_cents.integer' => 'O preço deve ser um número inteiro (centavos).',
            'interval.string' => 'O intervalo deve ser uma string.',
            'max_transactions.integer' => 'O número máximo de transações deve ser um inteiro.'
        ];
    }
}
