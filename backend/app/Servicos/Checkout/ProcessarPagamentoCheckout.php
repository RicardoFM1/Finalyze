<?php

namespace App\Servicos\Checkout;

use App\Models\Assinatura;
use App\Models\Plano;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;
use Illuminate\Support\Facades\Log;

class ProcessarPagamentoCheckout
{
    private function setupMercadoPago()
    {
        $token = config('services.mercadopago.token');
        if (!$token) {
            throw new \Exception('Mercado Pago Token missing');
        }
        MercadoPagoConfig::setAccessToken($token);
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);
    }

    public function executar(array $dados)
    {
        $usuario = auth()->user();
        if (!$usuario) {
            throw new \Exception('Usuário não autenticado', 401);
        }

        $this->setupMercadoPago();
        $client = new PaymentClient();

        $plano_id = $dados['plano_id'];
        $periodo_id = $dados['periodo_id'];

        $plano = Plano::findOrFail($plano_id);
        $periodo = $plano->periodos()->where('periodos.id', $periodo_id)->firstOrFail();
        $transactionAmount = $periodo->pivot->valor_centavos / 100;

        $assinatura = Assinatura::updateOrCreate(
            ['user_id' => $usuario->id, 'status' => 'pending', 'plano_id' => (int)$plano_id],
            [
                'periodo_id' => (int)$periodo_id,
                'inicia_em' => now(),
            ]
        );

        $payer = $dados['payer'] ?? [];
        $payerIdNumber = $payer['identification']['number'] ?? $usuario->cpf;

        if (!$payerIdNumber && config('app.env') !== 'production') {
            $payerIdNumber = '19119119100';
            $payer['identification']['type'] = 'CPF';
        }

        $paymentData = [
            "transaction_amount" => $transactionAmount,
            "payment_method_id" => $dados['payment_method_id'],
            "description" => ($dados['description'] ?? "Assinatura") . " - " . $plano->nome . " (" . $periodo->nome . ")",
            "payer" => [
                "email" => $usuario->email,
                "first_name" => explode(' ', $usuario->nome)[0] ?? 'Usuario',
                "last_name" => implode(' ', array_slice(explode(' ', $usuario->nome), 1)) ?? 'Usuario',
                "identification" => [
                    "type" => $payer['identification']['type'] ?? 'CPF',
                    "number" => $payerIdNumber
                ]
            ],
            "external_reference" => (string)$assinatura->id,
            "metadata" => [
                "user_id" => (string)$usuario->id,
                "plano_id" => (string)$plano_id,
                "periodo_id" => (string)$periodo_id,
                "assinatura_id" => (string)$assinatura->id,
                "quantidade_dias" => (string)$periodo->quantidade_dias
            ]
        ];

        if ($dados['payment_method_id'] === 'pix') {
            $paymentData["date_of_expiration"] = now()->addMinutes(10)->format('Y-m-d\TH:i:s.000O');
        } else {
            $paymentData["token"] = $dados['token'];
            $paymentData["issuer_id"] = (int)$dados['issuer_id'];
            $paymentData["installments"] = (int)$dados['installments'];
        }

        return $client->create($paymentData);
    }
}
