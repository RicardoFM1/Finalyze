<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLancamentoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tipo' => 'required|in:receita,despesa',
            'valor' => 'required|numeric',
            'categoria' => 'required|string',
            'data' => 'required|date',
            'descricao' => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'tipo.required' => 'O tipo é obrigatório.',
            'tipo.in' => 'O tipo deve ser receita ou despesa.',
            'valor.required' => 'O valor é obrigatório.',
            'valor.numeric' => 'O valor deve ser numérico.',
            'categoria.required' => 'A categoria é obrigatória.',
            'data.required' => 'A data é obrigatória.',
            'data.date' => 'A data deve ser válida.'
        ];
    }
}
