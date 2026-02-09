<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLancamentoRequest;
use App\Models\Lancamento;
use App\Servicos\Lancamentos\ListarLancamentos;
use App\Servicos\Lancamentos\CriarLancamento;
use App\Servicos\Lancamentos\EditarLancamento;
use App\Servicos\Lancamentos\DeletarLancamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LancamentoController extends Controller
{
    public function mostrar(Request $request, ListarLancamentos $servico)
    {
        return $servico->executar($request->all());
    }

    public function criar(StoreLancamentoRequest $request, CriarLancamento $servico)
    {
        return $servico->executar($request->validated());
    }

    public function editar(StoreLancamentoRequest $request, $lancamentoId, EditarLancamento $servico)
    {
        return $servico->executar((int)$lancamentoId, $request->validated());
    }

    public function deletar(Request $request, $lancamentoId, DeletarLancamento $servico)
    {
        $servico->executar((int)$lancamentoId);
        return response()->noContent();
    }
}
