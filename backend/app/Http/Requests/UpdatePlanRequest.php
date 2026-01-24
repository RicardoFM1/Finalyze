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
            'price' => 'numeric',
            'interval' => 'string',
            'max_transactions' => 'integer',
            'description' => 'string',
            'features' => 'array'
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'O nome deve ser uma string.',
            'price.numeric' => 'O preço deve ser numérico.',
            'interval.string' => 'O intervalo deve ser uma string.',
            'max_transactions.integer' => 'O número máximo de transações deve ser um inteiro.'
        ];
    }
}
