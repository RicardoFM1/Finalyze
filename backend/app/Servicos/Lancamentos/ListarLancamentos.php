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

        $perPage = $filtros['per_page'] ?? 10;

        return $query->paginate($perPage);
    }
}
