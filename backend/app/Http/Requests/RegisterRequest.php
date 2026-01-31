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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers()->symbols()],
            'cpf' => 'required|string|size:11|unique:users',
            'birth_date' => 'required|date|before:' . now()->subYears(18)->format('Y-m-d'),
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'O e-mail informado já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password' => 'A senha deve conter letras maiúsculas, minúsculas, números e símbolos.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.size' => 'O CPF deve ter exatamente 11 números.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'birth_date.required' => 'A data de nascimento é obrigatória.',
            'birth_date.date' => 'Data de nascimento inválida.',
            'birth_date.before' => 'Você deve ter pelo menos 18 anos para se cadastrar.',
        ];
    }
}
