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
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::SERVER);
    }

    public function executar(array $dados)
    {
        /** @var \App\Models\Usuario $usuario */
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

        // Cálculo de Prorrata
        $assinaturaAtiva = $usuario->assinaturaAtiva();
        $creditos = 0;
        $transactionAmount = $periodo->pivot->valor_centavos / 100;

        if ($assinaturaAtiva && $assinaturaAtiva->plano_id != $plano->id) {
            $calculadora = new CalculadoraProrata();
            $creditos = $calculadora->calcularCredito($assinaturaAtiva);
            $transactionAmount = max(0, $transactionAmount - $creditos);
        }

        Assinatura::where('user_id', $usuario->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);


        $assinatura = Assinatura::create([
            'user_id'    => $usuario->id,
            'plano_id'   => (int) $plano_id,
            'periodo_id' => (int) $periodo_id,
            'status'     => 'pending',
            'inicia_em'  => now(),
        ]);

        // Se o valor for zero, ativamos direto (Simulando aprovação)
        if ($transactionAmount <= 0) {
            $ativarServico = new AtivarPlanoUsuario();
            $ativarServico->executar((object)[
                'id' => 'CREDITO-' . time(),
                'transaction_amount' => 0,
                'payment_method_id' => 'credits',
                'status' => 'approved',
                'status_detail' => 'accredited',
                'metadata' => [
                    'user_id' => $usuario->id,
                    'plano_id' => $plano_id,
                    'periodo_id' => $periodo_id,
                    'assinatura_id' => $assinatura->id,
                    'quantidade_dias' => $periodo->quantidade_dias,
                    'creditos_prorrata' => $creditos
                ]
            ]);

            return (object)[
                'status' => 'approved',
                'status_detail' => 'accredited',
                'id' => 'CREDITO-' . time()
            ];
        }


        $payer = $dados['payer'] ?? [];
        $payerIdNumber = ($payer['identification']['number'] ?? null) ?: $usuario->cpf;

        if (!$payerIdNumber && config('app.env') !== 'production') {
            $payerIdNumber = '19119119100';
            $payer['identification']['type'] = 'CPF';
        }

        $paymentData = [
            "transaction_amount" => (float)$transactionAmount,
            "payment_method_id" => $dados['payment_method_id'],
            "description" => $plano->nome . " - " . $periodo->nome,
            "payer" => [
                "email" => $usuario->email,
                "identification" => [
                    "type" => "CPF",
                    "number" => $payerIdNumber
                ]
            ],
            "external_reference" => (string)$assinatura->id,
            "metadata" => [
                "user_id" => $usuario->id,
                "plano_id" => $plano_id,
                "periodo_id" => $periodo_id,
                "assinatura_id" => $assinatura->id,
                "quantidade_dias" => $periodo->quantidade_dias,
                "creditos_prorrata" => $creditos
            ]
        ];

        if ($dados['payment_method_id'] === 'pix') {
            $paymentData["date_of_expiration"] = now()->addMinutes(10)->format('Y-m-d\TH:i:s.000O');
        } else {
            $paymentData["token"] = $dados['token'];
            $paymentData["installments"] = (int)($dados['installments'] ?? 1);
            if (isset($dados['issuer_id'])) {
                $paymentData["issuer_id"] = $dados['issuer_id'];
            }
        }

        $response = $client->create($paymentData);

        // Ativação automática se for aprovado (comum em cartão)
        if (isset($response->status) && $response->status === 'approved') {
            try {
                $ativarServico = new AtivarPlanoUsuario();
                $ativarServico->executar($response);
            } catch (\Exception $e) {
                Log::error("Erro ao ativar plano após aprovação: " . $e->getMessage());
            }
        } else if ($dados['payment_method_id'] === 'pix') {
            // Registrar no histórico para Pix pendente
            try {
                \App\Models\HistoricoPagamento::create([
                    'user_id' => $usuario->id,
                    'assinatura_id' => $assinatura->id,
                    'valor_centavos' => (int)($transactionAmount * 100),
                    'status' => $response->status ?? 'pending',
                    'metodo_pagamento' => 'pix',
                    'mercado_pago_id' => (string)($response->id ?? null),
                    'pago_em' => ($response->status === 'approved') ? now() : null
                ]);
            } catch (\Exception $e) {
                Log::error("Erro ao salvar histórico de Pix: " . $e->getMessage());
            }
        }

        return $response;
    }
}
