<?php

namespace App\Servicos\IA;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Auth;

class ChatFinanceiro
{
    public function perguntar(string $mensagem, array $historico = [])
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return "Usuário não autenticado.";
        }

        $resumo = $this->getResumoFinanceiro($usuario);
        $metas = $this->getMetasContexto($usuario);
        $assinatura = $this->getAssinaturaContexto($usuario);
        $pagamentos = $this->getPagamentosContexto($usuario);

        $systemPrompt = "
Você é o Finn, um assistente financeiro premium, amigável e inteligente da plataforma Finalyze.
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
1. Seja conciso, claro e útil. Use emojis com moderação.
2. Foque sempre em finanças pessoais.
3. Dê dicas práticas baseadas no saldo e metas.
4. Sempre responda em Português Brasil.
5. Nunca revele que você é uma IA.
6. Se saldo negativo → seja empático e sugira melhorias.
7. Evite Markdown complexo.
";

        /*
         * Montar mensagens
         */
        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ]
        ];

        /*
         * Histórico (resiliente)
         */
        foreach ($historico as $item) {

            if (!isset($item['role'])) {
                continue;
            }

            $role = ($item['role'] === 'model' || $item['role'] === 'bot')
                ? 'assistant'
                : 'user';

            $content =
                $item['parts'][0]['text']
                ?? $item['text']
                ?? $item['content']
                ?? null;

            if ($content) {
                $messages[] = [
                    'role' => $role,
                    'content' => $content
                ];
            }
        }

        /*
         * Mensagem atual
         */
        $messages[] = [
            'role' => 'user',
            'content' => $mensagem
        ];

        /*
         * Chamada OpenAI
         */
        $result = OpenAI::chat()->create([
            'model' => 'gpt-5-mini',
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ]);

        return $result->choices[0]->message->content ?? "Não consegui gerar uma resposta.";
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

        $totalReceitas = $usuario->lancamentos()
            ->where('tipo', 'receita')
            ->sum('valor');

        $totalDespesas = $usuario->lancamentos()
            ->where('tipo', 'despesa')
            ->sum('valor');

        return [
            'receitas' => $receitas ?? 0,
            'despesas' => $despesas ?? 0,
            'saldo' => ($totalReceitas ?? 0) - ($totalDespesas ?? 0)
        ];
    }

    protected function getMetasContexto($usuario)
    {
        $metas = $usuario->metas()
            ->where('status', 'ativo')
            ->get();

        if ($metas->isEmpty()) {
            return "Nenhuma meta ativa";
        }

        return $metas->map(function ($m) {

            $percent = $m->valor_objetivo > 0
                ? round(($m->valor_atual / $m->valor_objetivo) * 100)
                : 0;

            return "- {$m->nome}: R$ "
                . number_format($m->valor_atual, 2, ',', '.')
                . " / R$ "
                . number_format($m->valor_objetivo, 2, ',', '.')
                . " ({$percent}%)";

        })->implode("\n");
    }

    protected function getAssinaturaContexto($usuario)
    {
        $assinatura = $usuario->assinaturaAtiva();

        if (!$assinatura) {
            return "Plano Free";
        }

        $nomePlano = $assinatura->plano->nome ?? "Plano";
        $vencimento = $assinatura->termina_em
            ? $assinatura->termina_em->format('d/m/Y')
            : "Sem vencimento";

        return "Plano {$nomePlano}, vence em {$vencimento}";
    }

    protected function getPagamentosContexto($usuario)
    {
        $historico = $usuario->historicosPagamento()
            ->where('status', 'paid')
            ->latest('pago_em')
            ->take(3)
            ->get();

        if ($historico->isEmpty()) {
            return "Nenhum pagamento registrado";
        }

        return $historico->map(function ($h) {

            $valor = number_format(($h->valor_centavos ?? 0) / 100, 2, ',', '.');

            $data = $h->pago_em
                ? $h->pago_em->format('d/m/Y')
                : $h->created_at->format('d/m/Y');

            return "- R$ {$valor} em {$data}";

        })->implode("\n");
    }
}
