<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // Adicione as regras de validação necessárias para processar o pagamento
        ];
    }

    public function messages()
    {
        return [
            // Mensagens personalizadas se necessário
        ];
    }
}
