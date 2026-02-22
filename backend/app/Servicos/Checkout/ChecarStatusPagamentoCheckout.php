<?php

namespace App\Servicos\Checkout;

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

class ChecarStatusPagamentoCheckout
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

    public function executar($id, AtivarPlanoUsuario $ativarPlanoServico)
    {
        $this->setupMercadoPago();
        $client = new PaymentClient();
        $payment = $client->get($id);

        // Sempre chama o serviço para registrar no histórico ou ativar
        $ativarPlanoServico->executar($payment);

        return [
            'status' => $payment->status,
            'status_detail' => $payment->status_detail
        ];
    }
}
