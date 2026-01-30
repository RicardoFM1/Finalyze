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
            'plan_id' => 'required|exists:planos,id',
        ];
    }

    public function messages()
    {
        return [
            'plan_id.required' => 'O ID do plano é obrigatório.',
            'plan_id.exists' => 'O plano selecionado é inválido.',
        ];
    }
}
