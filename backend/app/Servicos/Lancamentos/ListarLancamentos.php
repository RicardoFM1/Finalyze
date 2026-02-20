<?php

namespace App\Servicos\Lancamentos;

use Illuminate\Support\Facades\Auth;

class ListarLancamentos
{
    public function executar(array $filtros = [])
    {
        $query = Auth::user()->lancamentos()->latest();

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
            $query->where('categoria', $filtros['categoria']);
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
            $order = !empty($filtros['sort_order']) ? strtoupper($filtros['sort_order']) : 'DESC';
            $query->orderBy($filtros['sort_by'], $order);
        }

        $queryFiltered = clone $query;
        $totais = [
            'receita' => (clone $queryFiltered)->where('tipo', 'receita')->sum('valor'),
            'despesa' => (clone $queryFiltered)->where('tipo', 'despesa')->sum('valor'),
        ];

        $perPage = $filtros['per_page'] ?? 10;

        $paginated = $query->paginate($perPage, ['*'], 'page', $page);

        return \response()->json([
            'data' => $paginated->items(),
            'total' => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'totais' => $totais
        ]);
    }
}
