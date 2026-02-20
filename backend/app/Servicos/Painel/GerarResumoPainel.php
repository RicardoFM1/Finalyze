<?php

namespace App\Servicos\Painel;

use Illuminate\Support\Facades\Auth;

class GerarResumoPainel
{
    public function executar(array $filtros = [])
    {
        $usuario = Auth::user();
        $filtrosHash = md5(json_encode($filtros));
        $cacheKey = "user_summary_{$usuario->id}_{$filtrosHash}";

        $calc = function () use ($usuario, $filtros) {
            $query = $usuario->lancamentos();

            // Aplicar Filtros Gerais
            if (!empty($filtros['descricao'])) {
                $query->where('descricao', 'like', '%' . $filtros['descricao'] . '%');
            }
            if (!empty($filtros['categoria'])) {
                $query->where('categoria', $filtros['categoria']);
            }
            if (!empty($filtros['tipo']) && $filtros['tipo'] !== 'todos') {
                $query->where('tipo', $filtros['tipo']);
            }
            if (!empty($filtros['valor'])) {
                $query->where('valor', $filtros['valor']);
            }

            // Aplicar Filtro de Data
            if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
                $query->whereBetween('data', [$filtros['data_inicio'], $filtros['data_fim']]);
            } elseif (!empty($filtros['data'])) {
                $data = $filtros['data'];
                if (str_contains($data, ' to ')) {
                    $parts = explode(' to ', $data);
                    $query->whereBetween('data', [$parts[0], $parts[1] ?? $parts[0]]);
                } else {
                    $query->whereDate('data', $data);
                }
            } else {
                // Default: Mês Atual se não houver NENHUM outro filtro
                // Se houver categoria/descrição mas não data, talvez mostrar histórico geral?
                // Decisão: Se não houver filtro de data, pegamos o mês atual para o resumo financeiro.
                $query->whereMonth('data', now()->month)
                    ->whereYear('data', now()->year);
            }

            $receita = (clone $query)->where('tipo', 'receita')->sum('valor');
            $despesa = (clone $query)->where('tipo', 'despesa')->sum('valor');

            // Saldo Gerar vs Saldo Período
            // Se houver filtros, o saldo mostrado deve ser o resultado daquela busca
            if (!empty($filtros)) {
                $saldo = $receita - $despesa;
                // Retornamos todos os movimentos que batem com o filtro para o dashboard mostrar "o que gerou isso"
                $recentes = $query->orderBy('data', 'desc')->orderBy('id', 'desc')->get();
            } else {
                $totalRec = $usuario->lancamentos()->where('tipo', 'receita')->sum('valor');
                $totalDes = $usuario->lancamentos()->where('tipo', 'despesa')->sum('valor');
                $saldo = $totalRec - $totalDes;
                // Sem filtro, mostramos apenas os 5 mais recentes
                $recentes = $query->orderBy('data', 'desc')->orderBy('id', 'desc')->take(5)->get();
            }

            return [
                'receita' => (float) $receita,
                'despesa' => (float) $despesa,
                'saldo' => (float) $saldo,
                'atividades_recentes' => $recentes
            ];
        };

        if (!empty($filtros)) {
            return $calc();
        }

        return cache()->remember($cacheKey, now()->addMinutes(10), $calc);
    }
}
