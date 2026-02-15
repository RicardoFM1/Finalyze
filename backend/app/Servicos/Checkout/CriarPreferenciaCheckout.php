<?php

namespace App\Servicos\Checkout;

use App\Models\Assinatura;
use App\Models\Plano;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use Illuminate\Support\Facades\Log;

class CriarPreferenciaCheckout
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
        $this->setupMercadoPago();
        $client = new PreferenceClient();

        $planoId = $dados['plano_id'];
        $periodoId = $dados['periodo_id'];

        $plano = Plano::findOrFail($planoId);
        $periodo = $plano->periodos()->where('periodos.id', $periodoId)->firstOrFail();

        $items = [[
            "title" => $plano->nome . " (" . $periodo->nome . ")",
            "quantity" => 1,
            "unit_price" => $periodo->pivot->valor_centavos / 100
        ]];

        $baseUrl = 'https://roentgenographic-unsensuous-shizue.ngrok-free.dev';

        Log::info('Creating Mercado Pago Preference', [
            'baseUrl' => $baseUrl,
            'items' => $items
        ]);

        $preference = $client->create([
            "items" => $items,
            "back_urls" => [
                "success" => $baseUrl . "/?status=success",
                "failure" => $baseUrl . "/?status=failure",
                "pending" => $baseUrl . "/?status=pending"
            ],
            "auto_return" => "approved",
            "notification_url" => $baseUrl . "/api/webhook/mercadopago"
        ]);

        Assinatura::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->where('mercado_pago_id', '!=', $preference->id)
            ->update(['status' => 'cancelled']);

        Assinatura::updateOrCreate(
            ['user_id' => auth()->id(), 'status' => 'pending', 'mercado_pago_id' => $preference->id],
            [
                'plano_id' => $plano->id,
                'periodo_id' => $periodo->id
            ]
        );

        return $preference->id;
    }
}
