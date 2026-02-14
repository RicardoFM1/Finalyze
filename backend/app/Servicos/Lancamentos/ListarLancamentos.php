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

        if (!empty($filtros['data'])) {
            $query->whereDate('data', $filtros['data']);
        }

        if (!empty($filtros['valor'])) {
            $query->where('valor', $filtros['valor']);
        }

        if (!empty($filtros['sort_by'])) {
            $order = !empty($filtros['sort_order']) ? strtoupper($filtros['sort_order']) : 'DESC';
            $query->orderBy($filtros['sort_by'], $order);
        }

        $perPage = $filtros['per_page'] ?? 10;
        $page = $filtros['page'] ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
