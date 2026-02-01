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
            'nome' => 'string|max:255',
            'limite_lancamentos' => 'integer',
            'descricao' => 'nullable|string',
            'recursos' => 'array|min:1',
            'recursos.*' => 'exists:recursos,id',
            'periodos' => 'array|min:1',
            'periodos.*.id' => 'required|exists:periodos,id',
            'periodos.*.valor_centavos' => 'required|integer|min:0',
            'periodos.*.percentual_desconto' => 'nullable|integer|min:0|max:100',
            'ativo' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'nome.string' => 'O nome deve ser uma string.',
            'valor_centavos.integer' => 'O preço deve ser um número inteiro (centavos).',
            'limite_lancamentos.integer' => 'O número máximo de transações deve ser um inteiro.',
            'recursos.min' => 'É necessário informar pelo menos uma função.'
        ];
    }
}
