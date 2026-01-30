<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric',
            'category' => 'required|string',
            'date' => 'required|date',
            'description' => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'type.required' => 'O tipo é obrigatório.',
            'type.in' => 'O tipo deve ser income ou expense.',
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser numérico.',
            'category.required' => 'A categoria é obrigatória.',
            'date.required' => 'A data é obrigatória.',
            'date.date' => 'A data deve ser válida.'
        ];
    }
}
