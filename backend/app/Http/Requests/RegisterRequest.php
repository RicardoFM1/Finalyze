<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'senha' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers()->symbols()],
            'cpf' => 'required|string|size:11|unique:usuarios',
            'data_nascimento' => 'required|date|before:18 years ago',
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'O e-mail informado já está cadastrado.',
            'senha.required' => 'A senha é obrigatória.',
            'senha.confirmed' => 'A confirmação de senha não confere.',
            'senha.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'senha' => 'A senha deve conter letras maiúsculas, minúsculas, números e símbolos.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.size' => 'O CPF deve ter exatamente 11 números.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'data_nascimento.required' => 'A data de nascimento é obrigatória.',
            'data_nascimento.date' => 'A data de nascimento deve ser válida.',
            'data_nascimento.before' => 'Você deve ter pelo menos 18 anos.',
        ];
    }
}
