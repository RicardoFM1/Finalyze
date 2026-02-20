<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePreferenceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'plano_id' => 'required|exists:planos,id',
            'periodo_id' => 'required|exists:periodos,id',
        ];
    }

    public function messages()
    {
        return [
            'plano_id.required' => 'O ID do plano é obrigatório.',
            'plano_id.exists' => 'O plano selecionado é inválido.',
            'periodo_id.required' => 'O ID do período é obrigatório.',
            'periodo_id.exists' => 'O período selecionado é inválido.',
        ];
    }
}