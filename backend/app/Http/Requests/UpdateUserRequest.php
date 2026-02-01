<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->user()->id ?? null;
        return [
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:usuarios,email,' . $userId,
            'avatar' => 'nullable|image|max:2048',
            'cpf' => 'nullable|string|size:11|unique:usuarios,cpf,' . $userId,
            'data_nascimento' => 'nullable|date|before:18 years ago'
        ];
    }

    public function messages()
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'avatar.image' => 'O avatar deve ser uma imagem.',
            'avatar.max' => 'O avatar não pode ter mais de 2MB.',
            'cpf.size' => 'O CPF deve ter 11 dígitos.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'data_nascimento.date' => 'A data de nascimento deve ser válida.',
            'data_nascimento.before' => 'Você deve ter pelo menos 18 anos.',
        ];
    }
}
