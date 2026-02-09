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
            'payment_method_id' => 'required|string',
            'token' => 'nullable|string',
            'plano_id' => 'required|exists:planos,id',
            'periodo_id' => 'required|exists:periodos,id',
            'description' => 'nullable|string',
            'installments' => 'nullable|integer',
            'issuer_id' => 'nullable|integer',
            'payer' => 'nullable|array',
            'payer.email' => 'nullable|email',
            'payer.identification' => 'nullable|array',
            'payer.identification.type' => 'nullable|string',
            'payer.identification.number' => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [];
    }
}
