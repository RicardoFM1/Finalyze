<?php

namespace App\Servicos\IA;

use Gemini\Data\Content;
use Gemini\Enums\Role;
use Gemini\Laravel\Facades\Gemini;
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
        $planos = $this->getPlanosDisponiveis();

        $systemPrompt = "Você é o Finn, um assistente financeiro premium, amigável e inteligente da plataforma Finalyze.
        Seu objetivo é ajudar o usuário a gerenciar melhor seu dinheiro, dar dicas de economia e analisar seus gastos.
        
        Dados Financeiros (Mês Atual):
        - Saldo Atual: R$ " . number_format($resumo['saldo'], 2, ',', '.') . "
        - Receitas: R$ " . number_format($resumo['receitas'], 2, ',', '.') . "
        - Despesas: R$ " . number_format($resumo['despesas'], 2, ',', '.') . "
        
        Suas Metas: {$metas}
        Sua Assinatura Atual: {$assinatura}
        Últimos Pagamentos: {$pagamentos}
        Planos Disponíveis para Upgrade: {$planos}

        Instruções de Comportamento:
        1. Identifique-se como Finn. Seja conciso, mas elegante. Use emojis com moderação.
        2. Analise os dados acima para dar respostas personalizadas.
        3. Se o usuário perguntar algo fora do contexto financeiro ou do sistema Finalyze, traga suavemente de volta.
        4. Sempre responda em Português Brasil.
        5. Nunca revele que você é uma IA comercial.
        6. Não use Markdown complexo como tabelas grandes.
        7. Use o histórico de mensagens abaixo para manter o contexto da conversa.";

        $model = Gemini::generativeModel('gemini-1.5-flash')
            ->withSystemInstruction(Content::parse($systemPrompt));

        $chat = $model->startChat(history: array_map(function ($m) {
            return Content::parse(
                part: $m['content'],
                role: $m['role'] === 'user' ? Role::USER : Role::MODEL
            );
        }, $historico));

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

    protected function getMetasContexto($usuario)
    {
        $metas = $usuario->metas()->where('status', 'ativo')->get();
        if ($metas->isEmpty()) return "Nenhuma meta ativa no momento.";

        return $metas->map(fn($m) => "{$m->titulo} (Alvo: R$ {$m->valor_objetivo})")->implode(', ');
    }

    protected function getAssinaturaContexto($usuario)
    {
        $assinatura = $usuario->assinaturaAtiva();
        if (!$assinatura) return "Sem plano premium ativo (Plano Free).";

        $nomePlano = $assinatura->plano->nome;
        $vencimento = $assinatura->termina_em->format('d/m/Y');
        return "Plano {$nomePlano}, vence em {$vencimento}.";
    }

    protected function getPagamentosContexto($usuario)
    {
        $historico = $usuario->historicosPagamento()->latest()->take(3)->get();
        if ($historico->isEmpty()) return "Nenhum pagamento registrado.";

        return $historico->map(fn($h) => "R$ {$h->valor_pago} em {$h->created_at->format('d/m/Y')}")->implode('; ');
    }

    protected function getPlanosDisponiveis()
    {
        $planos = \App\Models\Plano::where('ativo', true)->with('periodos')->get();
        return $planos->map(function ($p) {
            $valores = $p->periodos->map(fn($per) => "{$per->nome}: R$ " . ($per->pivot->valor_centavos / 100))->implode(', ');
            return "{$p->nome} ({$valores})";
        })->implode(' | ');
    }
}
