<?php

namespace App\Http\Controllers;

use App\Servicos\Lembretes\AtualizarLembrete;
use App\Servicos\Lembretes\CriarLembrete;
use App\Servicos\Lembretes\ExcluirLembrete;
use App\Servicos\Lembretes\ListarLembretes;
use App\Servicos\Lembretes\ReativarLembrete;
use Illuminate\Http\Request;

class LembreteController extends Controller
{
    public function index(Request $request, ListarLembretes $servico)
    {
        return response()->json($servico->executar(app('workspace_id')));
    }

    public function store(Request $request, CriarLembrete $servico)
    {
        $dados = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'icone' => 'nullable|string',
            'cor' => 'nullable|string',
            'prazo' => 'nullable|date',
            'hora' => 'nullable|string',
            'notificacao_site' => 'nullable|boolean',
            'notificacao_email' => 'nullable|boolean',
            'status' => 'nullable|in:andamento,concluido'
        ]);

        $dados['usuario_id'] = app('workspace_id');

        return response()->json($servico->executar($dados), 201);
    }

    public function update(Request $request, $id, AtualizarLembrete $servico)
    {
        $dados = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'descricao' => 'nullable|string',
            'icone' => 'nullable|string',
            'cor' => 'nullable|string',
            'prazo' => 'nullable|date',
            'hora' => 'nullable|string',
            'notificacao_site' => 'nullable|boolean',
            'notificacao_email' => 'nullable|boolean',
            'status' => 'nullable|in:andamento,concluido'
        ]);

        return response()->json($servico->executar($id, $dados));
    }

    public function destroy($id, ExcluirLembrete $servico)
    {
        $servico->executar($id);
        return response()->json(['message' => 'Lembrete movido para inativos.']);
    }

    public function reativar($id, ReativarLembrete $servico)
    {
        return response()->json($servico->executar($id));
    }
}
