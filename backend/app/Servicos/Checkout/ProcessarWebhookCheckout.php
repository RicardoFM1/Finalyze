<?php

namespace App\Servicos\Checkout;

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;
use Illuminate\Support\Facades\Log;

class ProcessarWebhookCheckout
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

    public function executar(array $dados, AtivarPlanoUsuario $ativarPlanoServico)
    {
        Log::info('Mercado Pago Webhook Received', $dados);

        if (($dados['type'] ?? '') === 'payment' && isset($dados['data']['id'])) {
            $paymentId = $dados['data']['id'];

            try {
                $this->setupMercadoPago();
                $client = new PaymentClient();

                $payment = $client->get($paymentId);
                Log::info("Payment Update (Webhook): ID {$paymentId} is now {$payment->status}");

                $ativarPlanoServico->executar($payment);
            } catch (\Exception $e) {
                Log::error('Error processing webhook payment: ' . $e->getMessage());
            }
        }
    }
}
