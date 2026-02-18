<?php

namespace App\Http\Controllers;

use App\Servicos\IA\ChatFinanceiro;
use Illuminate\Http\Request;

class ChatIaController extends Controller
{
    public function perguntar(Request $request, ChatFinanceiro $servico)
    {
        $request->validate([
            'mensagem' => 'required|string|max:1000',
            'historico' => 'nullable|array'
        ]);

        $usuario = $request->user();

        try {
            // Pegamos o histórico enviado pelo frontend
            $historico = $request->historico ?? [];

            $resposta = $servico->perguntar($request->mensagem, $historico);

            return response()->json([
                'resposta' => $resposta
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro Finn AI (Stateless): ' . $e->getMessage(), [
                'user_id' => $usuario->id,
                'exception' => $e
            ]);

            return response()->json([
                'error' => 'O Finn está descansando um pouco. Tente novamente em alguns instantes.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
