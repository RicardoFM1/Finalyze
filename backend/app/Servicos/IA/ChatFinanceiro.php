<?php

namespace App\Servicos\IA;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class ChatFinanceiro
{
    public function perguntar(string $mensagem, array $historico = [], string $locale = 'pt-BR')
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return "Usuário não autenticado.";
        }

        $resumo = $this->getResumoFinanceiro($usuario);
        $metas = $this->getMetasContexto($usuario);
        $assinatura = $this->getAssinaturaContexto($usuario);
        $pagamentos = $this->getPagamentosContexto($usuario);
        $recentes = $this->getLancamentosRecentesContexto($usuario);

        $idiomaNome = $locale === 'en' ? 'Inglês' : ($locale === 'es' ? 'Espanhol' : 'Português Brasil');

        $systemPrompt = "
Você é o Finn, um assistente financeiro premium, amigável e inteligente da plataforma Finalyze.
Seu objetivo é ajudar o usuário a gerenciar melhor seu dinheiro, dar dicas de economia e analisar seus gastos.

O usuário prefere o idioma: {$idiomaNome} (Código: {$locale}).

Contexto do Usuário (ESTA É A VERDADE ATUAL E ABSOLUTA DO BANCO DE DADOS):
- Nome: {$usuario->nome}
- Saldo Geral (Total Acumulado): R$ " . number_format($resumo['saldo'], 2, ',', '.') . "
- Receitas (Deste Mês): R$ " . number_format($resumo['receitas'], 2, ',', '.') . "
- Despesas (Deste Mês): R$ " . number_format($resumo['despesas'], 2, ',', '.') . "
- Receitas (Ano Atual): R$ " . number_format($resumo['receitas_ano'], 2, ',', '.') . "
- Despesas (Ano Atual): R$ " . number_format($resumo['despesas_ano'], 2, ',', '.') . "

Últimos Lançamentos:
{$recentes}

Metas Financeiras:
{$metas}

Assinatura:
{$assinatura}

Pagamentos Recentes de Assinatura:
{$pagamentos}

Instruções de Comportamento:
1. Seja conciso, claro e útil. Use emojis com moderação.
2. Foque sempre em finanças pessoais.
3. Dê dicas práticas baseadas no saldo e metas.
4. DETECÇÃO DE IDIOMA: Embora o idioma preferido seja {$idiomaNome}, responda SEMPRE no idioma em que o usuário falar com você. Se ele disser 'Hello', responda em Inglês. Se disser 'Hola', em Espanhol. Caso contrário, use {$idiomaNome}.
4. DETECÇÃO DE IDIOMA: Detecte o idioma da mensagem do usuário e responda SEMPRE no mesmo idioma. Se o usuário escrever em inglês, responda em inglês. Se escrever em espanhol, responda em espanhol. O idioma padrão é Português Brasil.
5. Nunca revele que você é uma IA ou um modelo de linguagem.
6. Se saldo negativo → seja empático e sugira melhorias.
7. Evite Markdown complexo.
8. IMPORTANTE: Priorize SEMPRE os dados do 'Contexto do Usuário' e 'Últimos Lançamentos' acima em vez de dados citados no histórico de mensagens. O saldo geral é a soma de TUDO o que o usuário já lançou menos despesas.
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

            $role = ($item['role'] === 'model' || $item['role'] === 'bot' || $item['role'] === 'assistant')
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
         * Chamada MistralAI
         */
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.mistral.key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.mistral.ai/v1/chat/completions', [
            'model' => 'mistral-large-latest',
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 150,
        ]);

        if ($response->failed()) {
            return "Não consegui gerar uma resposta. (Erro na API do Mistral)";
        }

        $result = $response->json();

        return $result['choices'][0]['message']['content'] ?? "Não consegui gerar uma resposta.";
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

        $receitasAno = $usuario->lancamentos()
            ->where('tipo', 'receita')
            ->whereYear('data', $anoAtual)
            ->sum('valor');

        $despesasAno = $usuario->lancamentos()
            ->where('tipo', 'despesa')
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
            'receitas_ano' => $receitasAno ?? 0,
            'despesas_ano' => $despesasAno ?? 0,
            'saldo' => ($totalReceitas ?? 0) - ($totalDespesas ?? 0)
        ];
    }

    protected function getLancamentosRecentesContexto($usuario)
    {
        $recentes = $usuario->lancamentos()
            ->latest('data')
            ->latest('id')
            ->take(10)
            ->get();

        if ($recentes->isEmpty()) {
            return "Nenhum lançamento recente encontrado.";
        }

        return $recentes->map(function ($l) {
            $valor = number_format($l->valor, 2, ',', '.');
            $data = $l->data->format('d/m/Y');
            $tipo = ucfirst($l->tipo);
            return "- [{$data}] {$tipo}: {$l->descricao} (R$ {$valor}) - Categoria: {$l->categoria}";
        })->implode("\n");
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
