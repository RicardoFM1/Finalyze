<?php

namespace App\Servicos\IA;

use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\Content;
use Illuminate\Support\Facades\Auth;

class ChatFinanceiro
{
    public function perguntar(string $mensagem, array $historico = [])
    {
        $usuario = Auth::user();

        $resumo = $this->getResumoFinanceiro($usuario);
        $metas = $this->getMetasContexto($usuario);
        $assinatura = $this->getAssinaturaContexto($usuario);
        $pagamentos = $this->getPagamentosContexto($usuario);

        $systemPrompt = "Você é o Finn, um assistente financeiro premium, amigável e inteligente da plataforma Finalyze.
        Seu objetivo é ajudar o usuário a gerenciar melhor seu dinheiro, dar dicas de economia e analisar seus gastos.
        
        Contexto do Usuário:
        - Nome: {$usuario->nome}
        - Saldo Atual: R$ " . number_format($resumo['saldo'], 2, ',', '.') . "
        - Receitas (Mês Atual): R$ " . number_format($resumo['receitas'], 2, ',', '.') . "
        - Despesas (Mês Atual): R$ " . number_format($resumo['despesas'], 2, ',', '.') . "
        
        Metas Financeiras:
        {$metas}
        
        Assinatura:
        {$assinatura}
        
        Pagamentos Recentes:
        {$pagamentos}
        
        Instruções de Comportamento:
        1. Seja conciso, mas elegante. Use emojis com moderação.
        2. Se o usuário perguntar algo fora do contexto financeiro, tente trazer suavemente de volta para o assunto de finanças.
        3. Dê dicas práticas baseadas no saldo e metas dele. 
        4. Sempre responda em Português Brasil.
        5. Nunca revele que você é um modelo de linguagem IA, aja como o Finn.
        6. Se o saldo for negativo, seja empático e sugira cortes de gastos.
        7. Não use Markdown complexo como tabelas grandes, prefira listas e negrito.";

        // Injetando instruções do sistema no início do histórico para garantir compatibilidade
        $historicoInstrucoes = array_merge([
            [
                'role' => 'user',
                'parts' => [['text' => $systemPrompt]]
            ],
            [
                'role' => 'model',
                'parts' => [['text' => "Entendido! Serei o Finn, o assistente financeiro da Finalyze. Pode contar comigo."]]
            ]
        ], $historico);

        $response = Gemini::geminiFlash()
            ->startChat(history: $historicoInstrucoes)
            ->sendMessage($mensagem);

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

    protected function getMetasContexto($usuario)
    {
        $metas = $usuario->metas()->where('status', 'ativo')->get();
        if ($metas->isEmpty()) return "Nenhuma meta ativa";

        return $metas->map(function ($m) {
            $percent = $m->valor_objetivo > 0 ? round(($m->valor_atual / $m->valor_objetivo) * 100) : 0;
            return "- {$m->nome}: R$ " . number_format($m->valor_atual, 2, ',', '.') .
                " / R$ " . number_format($m->valor_objetivo, 2, ',', '.') . " ({$percent}%)";
        })->implode("\n");
    }

    protected function getAssinaturaContexto($usuario)
    {
        $assinatura = $usuario->assinaturaAtiva();
        if (!$assinatura) return "Plano Free (sem assinatura premium)";

        $nomePlano = $assinatura->plano->nome;
        $vencimento = $assinatura->termina_em->format('d/m/Y');
        return "Plano {$nomePlano}, vence em {$vencimento}";
    }

    protected function getPagamentosContexto($usuario)
    {
        $historico = $usuario->historicosPagamento()
            ->where('status', 'paid')
            ->latest('pago_em')
            ->take(3)
            ->get();

        if ($historico->isEmpty()) return "Nenhum pagamento registrado";

        return $historico->map(function ($h) {
            $valor = number_format($h->valor_centavos / 100, 2, ',', '.');
            $data = $h->pago_em ? $h->pago_em->format('d/m/Y') : $h->created_at->format('d/m/Y');
            return "- R$ {$valor} em {$data}";
        })->implode("\n");
    }
}
