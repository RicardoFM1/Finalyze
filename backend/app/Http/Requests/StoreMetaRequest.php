<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMetaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:255',
            'tipo' => 'required|in:financeira,pessoal',
            'valor_objetivo' => 'nullable|numeric|min:0',
            'valor_objetivo' => 'nullable|numeric|min:0',
            'meta_quantidade' => 'nullable|integer|min:0',
            'atual_quantidade' => 'nullable|integer|min:0',
            'unidade' => 'nullable|string|max:50',
            'prazo' => 'nullable|date|after_or_equal:today',
            'status' => 'nullable|in:andamento,concluido,pausado,atrasado',
            'cor' => 'nullable|string|max:50',
            'icone' => 'nullable|string|max:50'
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'O título é obrigatório.',
            'tipo.required' => 'O tipo é obrigatório.',
            'tipo.in' => 'O tipo deve ser financeira ou pessoal.',
            'valor_objetivo.numeric' => 'O valor objetivo deve ser um número.',
            'valor_objetivo.numeric' => 'O valor objetivo deve ser um número.',
            'meta_quantidade.integer' => 'A quantidade meta deve ser um número inteiro.',
            'atual_quantidade.integer' => 'A quantidade atual deve ser um número inteiro.',
            'prazo.date' => 'O prazo deve ser uma data válida.',
            'prazo.after_or_equal' => 'O prazo deve ser hoje ou uma data futura.',
            'status.in' => 'O status deve ser andamento, concluido, pausado ou atrasado.',
            'cor.string' => 'A cor deve ser uma string.',
            'icone.string' => 'O ícone deve ser uma string.'
        ];
    }
}
