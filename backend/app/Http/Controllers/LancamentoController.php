<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLancamentoRequest;
use App\Models\Lancamento;
use Illuminate\Support\Facades\Auth;

class LancamentoController extends Controller
{
    public function mostrar()
    {
        return Auth::user()->lancamentos()->latest()->get();
    }

    public function criar(StoreLancamentoRequest $request)
    {
        $usuario = Auth::user();

        $validated = $request->validated();

        return $usuario->lancamentos()->create($validated);
    }
}
