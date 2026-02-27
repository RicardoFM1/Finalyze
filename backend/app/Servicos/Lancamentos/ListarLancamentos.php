<?php

namespace App\Servicos\Lancamentos;

use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class ListarLancamentos
{
    public function executar(array $filtros = [])
    {
        $workspaceId = app('workspace_id');
        $usuario = Usuario::findOrFail($workspaceId);
        $query = $usuario->lancamentos()->orderBy('data', 'desc')->orderBy('id', 'desc');

        if (!empty($filtros['search'])) {
            $search = $filtros['search'];
            $query->where(function ($q) use ($search) {
                $q->where('descricao', 'like', "%{$search}%")
                    ->orWhere('categoria', 'like', "%{$search}%");
            });
        }

        if (!empty($filtros['descricao'])) {
            $query->where('descricao', 'like', "%{$filtros['descricao']}%");
        }

        if (!empty($filtros['categoria'])) {
            if (is_array($filtros['categoria'])) {
                $query->whereIn('categoria', $filtros['categoria']);
            } else {
                $query->where('categoria', $filtros['categoria']);
            }
        }

        if (!empty($filtros['tipo']) && $filtros['tipo'] !== 'todos') {
            $query->where('tipo', $filtros['tipo']);
        }

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
        }

        if (!empty($filtros['valor'])) {
            $query->where('valor', $filtros['valor']);
        }

        if (!empty($filtros['sort_by'])) {
            $allowedSortBy = ['id', 'data', 'descricao', 'categoria', 'tipo', 'valor', 'forma_pagamento', 'created_at', 'updated_at'];
            if (in_array($filtros['sort_by'], $allowedSortBy, true)) {
                $order = !empty($filtros['sort_order']) ? strtoupper($filtros['sort_order']) : 'DESC';
                $query->reorder()->orderBy($filtros['sort_by'], $order === 'ASC' ? 'ASC' : 'DESC');
            }
        }

        $queryFiltered = clone $query;
        $totais = [
            'receita' => (clone $queryFiltered)->where('tipo', 'receita')->sum('valor'),
            'despesa' => (clone $queryFiltered)->where('tipo', 'despesa')->sum('valor'),
        ];

        $perPage = isset($filtros['per_page']) ? (int) $filtros['per_page'] : 10;
        $perPage = max(5, min(50, $perPage));
        $page = isset($filtros['page']) ? max(1, (int) $filtros['page']) : 1;

        $paginated = $query->paginate(
            $perPage,
            ['id', 'descricao', 'categoria', 'tipo', 'valor', 'data', 'forma_pagamento', 'created_at', 'updated_at'],
            'page',
            $page
        );

        $data_inicio = $filtros['data_inicio'] ?? null;
        $data_fim = $filtros['data_fim'] ?? null;
        $hasDateFilter = !empty($filtros['data']) || (!empty($filtros['data_inicio']) && !empty($filtros['data_fim']));

        if (!$data_inicio && !empty($filtros['data'])) {
            if (str_contains($filtros['data'], ' to ')) {
                $parts = explode(' to ', $filtros['data']);
                $data_inicio = $parts[0];
                $data_fim = $parts[1] ?? $parts[0];
            } else {
                $data_inicio = $filtros['data'];
                $data_fim = $filtros['data'];
            }
        }

        $isFiltered = !empty($filtros['search']) || !empty($filtros['descricao']) ||
            !empty($filtros['categoria']) || (!empty($filtros['tipo']) && $filtros['tipo'] !== 'todos') ||
            $hasDateFilter || !empty($filtros['valor']);

        return response()->json([
            'data' => $paginated->items(),
            'total' => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'totais' => $totais,
            'periodo_label' => $isFiltered ? "filters.period_custom" : "filters.period_all",
            'data_inicio' => $data_inicio,
            'data_fim' => $data_fim
        ]);
    }
}
