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
        Lancamento::validarLimiteLancamentos($usuario->id);

        $validated = $request->validated();

        return $usuario->lancamentos()->create($validated);
    }

    public function editar(StoreLancamentoRequest $request, $lancamentoId)
    {
        $usuario = Auth::user();

        $validated = $request->validated();
        $lancamento = $usuario->lancamentos()->findOrFail($lancamentoId);
        if (!$lancamento) {
            abort(404, 'Lançamento não encontrado ou não pertence ao usuário.');
        }
        if ($lancamento->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para editar este lançamento.');
        }
        $lancamento->update($validated);
        return $lancamento;
    }
    public function deletar(StoreLancamentoRequest $request, $lancamentoId)
    {
        $usuario = Auth::user();

        $validated = $request->validated();

        $lancamento = $usuario->lancamentos()->findOrFail($lancamentoId);
        if (!$lancamento) {
            abort(404, 'Lançamento não encontrado ou não pertence ao usuário.');
        }
        if ($lancamento->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para editar este lançamento.');
        }
        $lancamento->delete();
    }
}
