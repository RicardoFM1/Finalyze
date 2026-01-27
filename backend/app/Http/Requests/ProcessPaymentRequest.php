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
            'transaction_amount' => 'required|numeric',
            'token' => 'required|string',
            'description' => 'required|string',
            'installments' => 'required|integer',
            'payment_method_id' => 'required|string',
            'issuer_id' => 'required|integer',
            'payer' => 'required|array',
            'payer.email' => 'required|email',
            'payer.identification' => 'required|array',
            'payer.identification.type' => 'required|string',
            'payer.identification.number' => 'required|string'
        ];
        ];
    }

    public function messages()
    {
        return [
            // Mensagens personalizadas se necessário
        ];
    }
}
