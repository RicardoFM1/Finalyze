<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HandleWebhookRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // Adicione as regras de validação necessárias para o webhook
        ];
    }

    public function messages()
    {
        return [
            // Mensagens personalizadas se necessário
        ];
    }
}
