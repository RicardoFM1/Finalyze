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
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::SERVER);
    }

    public function executar(array $dados)
    {
        $this->setupMercadoPago();
        $client = new PreferenceClient();

        $planoId = $dados['plano_id'];
        $periodoId = $dados['periodo_id'];

        $plano = Plano::findOrFail($planoId);
        $periodo = $plano->periodos()->where('periodos.id', $periodoId)->firstOrFail();

        $usuario = auth()->user();
        $assinaturaAtiva = $usuario->assinaturaAtiva();
        $creditos = 0;
        $totalPagar = $periodo->pivot->valor_centavos / 100;

        if ($assinaturaAtiva && $assinaturaAtiva->plano_id != $plano->id) {
            $calculadora = new CalculadoraProrata();
            $creditos = $calculadora->calcularCredito($assinaturaAtiva);
            $totalPagar = max(0, $totalPagar - $creditos);
        }

        $items = [[
            "title" => $plano->nome . " (" . $periodo->nome . ")",
            "quantity" => 1,
            "unit_price" => (float)$totalPagar
        ]];

        $backendUrl = rtrim(config('app.url'), '/');
        $frontendUrl = rtrim(config('app.frontend_url') ?: config('app.url'), '/');

        Log::info('Creating Mercado Pago Preference', [
            'frontendUrl' => $frontendUrl,
            'backendUrl' => $backendUrl,
            'items' => $items,
            'user_id' => $usuario->id,
            'creditos_aplicados' => $creditos
        ]);

        $assinatura = Assinatura::where('user_id', $usuario->id)
            ->where('status', 'pending')
            ->where('plano_id', $plano->id)
            ->where('periodo_id', $periodo->id)
            ->latest()
            ->first();

        if (!$assinatura) {
            $assinatura = Assinatura::create([
                'user_id' => $usuario->id,
                'plano_id' => $plano->id,
                'periodo_id' => $periodo->id,
                'status' => 'pending'
            ]);
        }

        $preferenceData = [
            "items" => $items,
            "external_reference" => (string)$assinatura->id,
            "metadata" => [
                "user_id" => $usuario->id,
                "plano_id" => $plano->id,
                "assinatura_id" => $assinatura->id,
                "quantidade_dias" => $periodo->quantidade_dias,
                "creditos_prorrata" => $creditos
            ],
            
            "notification_url" => $backendUrl . "/api/webhook/mercadopago"
        ];

        $preference = $client->create($preferenceData);

        $assinatura->update(['mercado_pago_id' => $preference->id]);

        $historico = \App\Models\HistoricoPagamento::where('assinatura_id', $assinatura->id)->first();

        if ($historico) {
            $historico->update([
                'valor_centavos' => (int)($totalPagar * 100),
                'status' => 'pending',
                'metodo_pagamento' => 'preferência',
                'mercado_pago_id' => $preference->id,
            ]);
        } else {
            \App\Models\HistoricoPagamento::create([
                'user_id' => $usuario->id,
                'assinatura_id' => $assinatura->id,
                'valor_centavos' => (int)($totalPagar * 100),
                'status' => 'pending',
                'metodo_pagamento' => 'preferência',
                'mercado_pago_id' => $preference->id,
            ]);
        }

        $outrasPendentes = Assinatura::where('user_id', $usuario->id)
            ->where('status', 'pending')
            ->where('id', '!=', $assinatura->id)
            ->get();

        foreach ($outrasPendentes as $outra) {
            $outra->update(['status' => 'cancelled']);
            \App\Models\HistoricoPagamento::where('assinatura_id', $outra->id)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);
        }

        return [
            'id' => $preference->id,
            'creditos_prorrata' => $creditos
        ];
    }
}
