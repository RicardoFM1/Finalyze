<?php

namespace App\Servicos\Lancamentos;

use Illuminate\Support\Facades\Auth;

class ListarLancamentos
{
    public function executar(array $filtros = [])
    {
        $query = Auth::user()->lancamentos()->latest();

        // Filtro de busca
        if (!empty($filtros['search'])) {
            $search = $filtros['search'];
            $query->where(function ($q) use ($search) {
                $q->where('descricao', 'like', "%{$search}%")
                    ->orWhere('categoria', 'like', "%{$search}%");
            });
        }

        // Filtro por descrição
        if (!empty($filtros['descricao'])) {
            $query->where('descricao', 'like', "%{$filtros['descricao']}%");
        }

        // Filtro por categoria
        if (!empty($filtros['categoria'])) {
            $query->where('categoria', $filtros['categoria']);
        }

        // Filtro por tipo
        if (!empty($filtros['tipo']) && $filtros['tipo'] !== 'todos') {
            $query->where('tipo', $filtros['tipo']);
        }

        // Filtro por data
        if (!empty($filtros['data'])) {
            $query->whereDate('data', $filtros['data']);
        }

        // Filtro por valor
        if (!empty($filtros['valor'])) {
            $query->where('valor', $filtros['valor']);
        }

        // Ordenação
        if (!empty($filtros['sort_by'])) {
            $order = !empty($filtros['sort_order']) ? strtoupper($filtros['sort_order']) : 'DESC';
            $query->orderBy($filtros['sort_by'], $order);
        }

        $perPage = $filtros['per_page'] ?? 10;
        $page = $filtros['page'] ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
