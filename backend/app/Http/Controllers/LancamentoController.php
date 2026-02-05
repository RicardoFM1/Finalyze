<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLancamentoRequest;
use App\Models\Lancamento;
use Illuminate\Database\Eloquent\Model;
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
        $maximoLancamentos = Lancamento::LimiteLancamentos($usuario->id);
        
        $validated = $request->validated();

        return $usuario->lancamentos()->create($validated);
    }
}
