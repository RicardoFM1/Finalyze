<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->hasFile('avatar')) {
            $file = $this->file('avatar');
            \Log::info('Avatar Upload Debug:', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'error' => $file->getError(),
                'valid' => $file->isValid(),
                'path' => $file->getPathname(),
                'php_upload_max' => ini_get('upload_max_filesize'),
                'php_post_max' => ini_get('post_max_size'),
                'php_memory_limit' => ini_get('memory_limit')
            ]);
        } else {
            \Log::info('Avatar Upload Debug: No file received in request.');
        }
    }

    public function rules()
    {
        $userId = $this->user()->id ?? null;
        return [
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:usuarios,email,' . $userId,
            'avatar' => 'nullable|image|max:10240',
            'cpf' => 'nullable|string|size:11|unique:usuarios,cpf,' . $userId,
            'data_nascimento' => 'nullable|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d')
        ];
    }

    public function messages()
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'avatar.image' => 'O arquivo enviado deve ser uma imagem.',
            'avatar.max' => 'A imagem não pode ter mais de 10MB.',
            'avatar.uploaded' => 'Erro ao processar o upload da imagem. O arquivo pode ser muito grande para as configurações do servidor.',
            'cpf.size' => 'O CPF deve ter 11 dígitos.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'data_nascimento.date' => 'A data de nascimento deve ser válida.',
            'data_nascimento.before' => 'Você deve ter pelo menos 18 anos.',
        ];
    }
}
