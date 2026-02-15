<?php

namespace App\Servicos\IA;

use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Auth;

class ChatFinanceiro
{
    public function perguntar(string $mensagem, array $historico = [])
    {
        $usuario = Auth::user();

        $resumo = $this->getResumoFinanceiro($usuario);

        $systemPrompt = "Você é o Finn, um assistente financeiro premium, amigável e inteligente da plataforma Finalyze.
        Seu objetivo é ajudar o usuário a gerenciar melhor seu dinheiro, dar dicas de economia e analisar seus gastos.
        
        Contexto do Usuário:
        - Nome: {$usuario->nome}
        - Saldo Atual: R$ " . number_format($resumo['saldo'], 2, ',', '.') . "
        - Receitas (Mês Atual): R$ " . number_format($resumo['receitas'], 2, ',', '.') . "
        - Despesas (Mês Atual): R$ " . number_format($resumo['despesas'], 2, ',', '.') . "
        
        Instruções de Comportamento:
        1. Seja conciso, mas elegante. Use emojis com moderação.
        2. Se o usuário perguntar algo fora do contexto financeiro, tente trazer suavemente de volta para o assunto de finanças.
        3. Dê dicas práticas baseadas no saldo dele. 
        4. Sempre responda em Português Brasil.
        5. Nunca revele que você é um modelo de linguagem IA, aja como o Finn.
        6. Se o saldo for negativo, seja empático e sugira cortes de gastos.";

        // Usando o Facade do Laravel para simplificar
        $chat = Gemini::chat()->withSystemInstruction($systemPrompt);

        $response = $chat->sendMessage($mensagem);

        return $response->text();
    }

    protected function getResumoFinanceiro($usuario)
    {
        $hoje = now();
        $mesAtual = $hoje->month;
        $anoAtual = $hoje->year;

        $receitas = $usuario->lancamentos()
            ->where('tipo', 'receita')
            ->whereMonth('data', $mesAtual)
            ->whereYear('data', $anoAtual)
            ->sum('valor');

        $despesas = $usuario->lancamentos()
            ->where('tipo', 'despesa')
            ->whereMonth('data', $mesAtual)
            ->whereYear('data', $anoAtual)
            ->sum('valor');

        $totalReceitas = $usuario->lancamentos()->where('tipo', 'receita')->sum('valor');
        $totalDespesas = $usuario->lancamentos()->where('tipo', 'despesa')->sum('valor');

        return [
            'receitas' => $receitas,
            'despesas' => $despesas,
            'saldo' => $totalReceitas - $totalDespesas
        ];
    }
}
