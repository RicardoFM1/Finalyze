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
            'nome' => 'required|string|max:255',
            'limite_lancamentos' => 'required|integer',
            'descricao' => 'nullable|string',
            'recursos' => 'required|array|min:1',
            'recursos.*' => 'exists:recursos,id',
            'periodos' => 'required|array|min:1',
            'periodos.*.id' => 'required|exists:periodos,id',
            'periodos.*.valor_centavos' => 'required|integer|min:0',
            'periodos.*.percentual_desconto' => 'nullable|integer|min:0|max:100',
            'ativo' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'valor_centavos.required' => 'O preço é obrigatório.',
            'valor_centavos.integer' => 'O preço deve ser um número inteiro (centavos).',
            'valor_centavos.min' => 'O preço deve ser maior que zero.',
            'limite_lancamentos.required' => 'O número máximo de transações é obrigatório.',
            'recursos.required' => 'Pelo menos uma função deve ser informada.',
            'recursos.min' => 'É necessário informar pelo menos uma função.'
        ];
    }
}
