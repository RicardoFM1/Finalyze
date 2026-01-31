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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $userId,
            'avatar' => 'nullable|image|max:2048',
            'cpf' => 'nullable|string|size:11|unique:users,cpf,' . $userId,
            'birth_date' => 'nullable|date|before:' . now()->subYears(18)->format('Y-m-d')
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'avatar.image' => 'O avatar deve ser uma imagem.',
            'avatar.max' => 'O avatar não pode ter mais de 2MB.',
            'cpf.size' => 'O CPF deve ter 11 dígitos.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'birth_date.date' => 'Data de nascimento inválida.',
            'birth_date.before' => 'Você deve ter pelo menos 18 anos.'
        ];
    }
}
