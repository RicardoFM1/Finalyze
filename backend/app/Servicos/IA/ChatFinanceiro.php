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

        // Contexto financeiro (mês atual)
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

        $saldo = $receitas - $despesas;

        $formatarReal = fn($valor) => "R$ " . number_format($valor, 2, ',', '.');

        $metas = $usuario->metas->map(fn($m) => "- {$m->nome}: Alcançado " . $formatarReal($m->valor_atual) . " de " . $formatarReal($m->valor_objetivo))->implode("\n");
        $assinaturaAtiva = $usuario->assinaturaAtiva();
        $assinaturaProg = $assinaturaAtiva ? "Plano: {$assinaturaAtiva->plano->nome} ({$assinaturaAtiva->periodo->nome}) - Expira em " . $assinaturaAtiva->termina_em->format('d/m/Y') : "Nenhuma assinatura ativa";

        $pagamentos = $usuario->historicosPagamento()->where('status', 'paid')->latest()->take(3)->get()
            ->map(fn($p) => "- " . $formatarReal($p->valor_centavos / 100) . " em " . $p->pago_em->format('d/m/Y'))->implode("\n");

        $planosDisponiveis = \App\Models\Plano::with('periodos')->get()->map(function ($p) use ($formatarReal) {
            $periods = $p->periodos->map(fn($per) => "{$per->nome}: " . $formatarReal($per->pivot->valor_centavos / 100))->implode(', ');
            return "- {$p->nome} ($periods)";
        })->implode("\n");

        $systemPrompt = "Você é Finn, assistente financeiro premium da Finalyze.
            Seu objetivo é ajudar o usuário a entender receitas, despesas, saldo, metas e assinaturas.

            [CONTEXTO FINANCEIRO (Mês Atual)]
            Saldo: " . $formatarReal($saldo) . "
            Receitas: " . $formatarReal($receitas) . "
            Despesas: " . $formatarReal($despesas) . "

            [METAS]
            $metas

            [ASSINATURA]
            $assinaturaProg

            [PAGAMENTOS RECENTES]
            $pagamentos

            [PLANOS DISPONÍVEIS]
            $planosDisponiveis

            Regras:
            - Seja conciso, amigável e inteligente.
            - Use emojis moderadamente.
            - Responda sempre em Português Brasil.
            - Não use tabelas grandes.
            - Traga conversas fora do tema suavemente de volta.
            - Use o histórico de mensagens abaixo para manter o contexto da conversa.";

        $model = Gemini::generativeModel('gemini-1.5-flash')
            ->withSystemInstruction(Content::parse($systemPrompt));

        $chat = $model->startChat(history: array_map(function ($m) {
            return new Content(
                parts: [new \Gemini\Data\Part(text: $m['content'])],
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
