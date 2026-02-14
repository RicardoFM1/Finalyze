<?php

namespace App\Http\Controllers;

use App\Servicos\Anotacoes\AtualizarAnotacao;
use App\Servicos\Anotacoes\CriarAnotacao;
use App\Servicos\Anotacoes\ExcluirAnotacao;
use App\Servicos\Anotacoes\ListarAnotacoes;
use App\Servicos\Anotacoes\ReativarAnotacao;
use Illuminate\Http\Request;

class AnotacaoController extends Controller
{
    public function index(Request $request, ListarAnotacoes $servico)
    {
        return response()->json($servico->executar($request->user()->id));
    }

    public function store(Request $request, CriarAnotacao $servico)
    {
        $dados = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'icone' => 'nullable|string',
            'cor' => 'nullable|string',
            'prazo' => 'nullable|date',
            'status' => 'nullable|in:andamento,concluido'
        ]);

        $dados['usuario_id'] = $request->user()->id;

        return response()->json($servico->executar($dados), 201);
    }

    public function update(Request $request, $id, AtualizarAnotacao $servico)
    {
        $dados = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'icone' => 'nullable|string',
            'cor' => 'nullable|string',
            'prazo' => 'nullable|date',
            'status' => 'nullable|in:andamento,concluido'
        ]);

        return response()->json($servico->executar($id, $dados));
    }

    public function destroy($id, ExcluirAnotacao $servico)
    {
        $servico->executar($id);
        return response()->json(['message' => 'Anotação movida para inativas.']);
    }

    public function reativar($id, ReativarAnotacao $servico)
    {
        return response()->json($servico->executar($id));
    }
}
