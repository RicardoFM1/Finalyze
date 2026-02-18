<?php

namespace App\Http\Controllers;

use App\Servicos\IA\ChatFinanceiro;
use Illuminate\Http\Request;

class ChatIaController extends Controller
{
    public function index(Request $request)
    {
        $mensagens = $request->user()->mensagensChat()
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($mensagens);
    }

    public function perguntar(Request $request, ChatFinanceiro $servico)
    {
        $request->validate([
            'mensagem' => 'required|string|max:1000',
        ]);

        $usuario = $request->user();

        // Salvar mensagem do usuário
        $usuario->mensagensChat()->create([
            'role' => 'user',
            'texto' => $request->mensagem
        ]);

        try {
            // Pegar histórico para contexto (últimas 15 mensagens, excluindo a atual)
            $historico = $usuario->mensagensChat()
                ->where('role', '!=', 'pending') // Garantir que não pegamos temporários
                ->orderBy('created_at', 'desc')
                ->skip(1) // Pula a mensagem do usuário que acabamos de salvar
                ->take(15)
                ->get()
                ->reverse()
                ->map(fn($m) => [
                    'role' => $m->role === 'bot' ? 'model' : 'user',
                    'parts' => [['text' => $m->texto]]
                ])
                ->toArray();

            $resposta = $servico->perguntar($request->mensagem, $historico);

            // Salvar resposta do Finn
            $usuario->mensagensChat()->create([
                'role' => 'bot',
                'texto' => $resposta
            ]);

            return response()->json([
                'resposta' => $resposta
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro Finn AI: ' . $e->getMessage(), [
                'user_id' => $usuario->id,
                'exception' => $e
            ]);

            return response()->json([
                'error' => 'O Finn está descansando um pouco. Tente novamente em alguns instantes.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate(['texto' => 'required|string|max:1000']);

        $mensagem = $request->user()->mensagensChat()->findOrFail($id);

        if ($mensagem->role !== 'user') {
            return response()->json(['error' => 'Apenas mensagens do usuário podem ser editadas.'], 403);
        }

        $mensagem->update(['texto' => $request->texto]);

        return response()->json(['message' => 'Mensagem atualizada.']);
    }

    public function destroy(Request $request, $id)
    {
        $mensagem = $request->user()->mensagensChat()->findOrFail($id);
        $mensagem->delete();

        return response()->json(['message' => 'Mensagem removida.']);
    }
}
