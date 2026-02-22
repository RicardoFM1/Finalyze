<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\Lembrete;
=======
use App\Servicos\Lembretes\AtualizarLembrete;
use App\Servicos\Lembretes\CriarLembrete;
use App\Servicos\Lembretes\ExcluirLembrete;
use App\Servicos\Lembretes\ListarLembretes;
use App\Servicos\Lembretes\ReativarLembrete;
>>>>>>> Ricardo
use Illuminate\Http\Request;

class LembreteController extends Controller
{
<<<<<<< HEAD
    public function index(Request $request)
    {
        $query = Lembrete::where('usuario_id', $request->user()->id);

        if ($request->filled('start')) {
            $query->where('data_aviso', '>=', $request->input('start'));
        }

        if ($request->filled('end')) {
            $query->where('data_aviso', '<=', $request->input('end'));
        }

        return response()->json($query->orderBy('data_aviso')->get());
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'titulo'        => 'required|string|max:255',
            'descricao'     => 'nullable|string',
            'categoria'     => 'nullable|string|max:80',
            'cor'           => 'nullable|string|max:20',
            'data_aviso'    => 'required|date',
            'data_lembrete' => 'nullable|date',
            'status'        => 'nullable|in:pendente,concluido,cancelado',
            'meta_id'       => 'nullable|integer|exists:metas,id',
        ]);

        $dados['data_lembrete'] = $dados['data_aviso'];
        $dados['usuario_id']    = $request->user()->id;
        $dados['status']        = $dados['status'] ?? 'pendente';

        $lembrete = Lembrete::create($dados);

        return response()->json($lembrete, 201);
    }

    public function update(Request $request, $id)
    {
        $dados = $request->validate([
            'titulo'        => 'required|string|max:255',
            'descricao'     => 'nullable|string',
            'categoria'     => 'nullable|string|max:80',
            'cor'           => 'nullable|string|max:20',
            'data_aviso'    => 'required|date',
            'data_lembrete' => 'nullable|date',
            'status'        => 'required|in:pendente,concluido,cancelado',
            'meta_id'       => 'nullable|integer|exists:metas,id',
        ]);

        $dados['data_lembrete'] = $dados['data_aviso'];
        $lembrete = Lembrete::where('usuario_id', $request->user()->id)->findOrFail($id);
        $lembrete->update($dados);

        return response()->json($lembrete->fresh());
    }

    public function patch(Request $request, $id)
    {
        $dados = $request->validate([
            'titulo'        => 'sometimes|required|string|max:255',
            'descricao'     => 'sometimes|nullable|string',
            'categoria'     => 'sometimes|nullable|string|max:80',
            'cor'           => 'sometimes|nullable|string|max:20',
            'data_aviso'    => 'sometimes|required|date',
            'data_lembrete' => 'sometimes|nullable|date',
            'status'        => 'sometimes|required|in:pendente,concluido,cancelado',
        ]);

        if (array_key_exists('data_aviso', $dados)) {
            $dados['data_lembrete'] = $dados['data_aviso'];
        }

        $lembrete = Lembrete::where('usuario_id', $request->user()->id)->findOrFail($id);
        $lembrete->update($dados);

        return response()->json($lembrete->fresh());
    }

    public function patchStatus(Request $request, $id)
    {
        $dados = $request->validate([
            'status' => 'required|in:pendente,concluido,cancelado',
        ]);

        $lembrete = Lembrete::where('usuario_id', $request->user()->id)->findOrFail($id);
        $lembrete->update(['status' => $dados['status']]);

        return response()->json($lembrete->fresh());
    }

    public function destroy(Request $request, $id)
    {
        $lembrete = Lembrete::where('usuario_id', $request->user()->id)->findOrFail($id);
        $lembrete->delete();

        return response()->json(null, 204);
=======
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
>>>>>>> Ricardo
    }
}
