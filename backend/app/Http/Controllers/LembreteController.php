<?php

namespace App\Http\Controllers;

use App\Servicos\Lembretes\AtualizarLembrete;
use App\Servicos\Lembretes\CriarLembrete;
use App\Servicos\Lembretes\DesativarLembrete;
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

        // Garante booleanos para PostgreSQL
        if (isset($dados['notificacao_site'])) $dados['notificacao_site'] = $dados['notificacao_site'] ? 'true' : 'false';
        if (isset($dados['notificacao_email'])) $dados['notificacao_email'] = $dados['notificacao_email'] ? 'true' : 'false';

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

        // Garante booleanos para PostgreSQL
        if (isset($dados['notificacao_site'])) $dados['notificacao_site'] = $dados['notificacao_site'] ? 'true' : 'false';
        if (isset($dados['notificacao_email'])) $dados['notificacao_email'] = $dados['notificacao_email'] ? 'true' : 'false';

        return response()->json($servico->executar($id, $dados));
    }

    public function destroy($id, DesativarLembrete $servico)
    {
        $servico->executar($id);
        return response()->json(['message' => 'Lembrete movido para inativos.']);
    }

    public function reativar($id, ReativarLembrete $servico)
    {
        return response()->json($servico->executar($id));
    }
}
