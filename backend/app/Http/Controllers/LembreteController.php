<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use App\Models\Lembrete;
use Illuminate\Http\Request;

class LembreteController extends Controller
{
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
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'categoria' => 'nullable|string|max:80',
            'cor' => 'nullable|string|max:20',
            'data_aviso' => 'required|date',
            'data_lembrete' => 'nullable|date',
            'status' => 'nullable|in:pendente,concluido,cancelado',
        ]);

        $dados['data_lembrete'] = $dados['data_aviso'];
        $dados['usuario_id'] = $request->user()->id;
        $dados['status'] = $dados['status'] ?? 'pendente';

        $lembrete = Lembrete::create($dados);

        return response()->json($lembrete, 201);
    }

    public function update(Request $request, $id)
    {
        $dados = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'categoria' => 'nullable|string|max:80',
            'cor' => 'nullable|string|max:20',
            'data_aviso' => 'required|date',
            'data_lembrete' => 'nullable|date',
            'status' => 'required|in:pendente,concluido,cancelado',
        ]);

        $dados['data_lembrete'] = $dados['data_aviso'];
        $lembrete = Lembrete::where('usuario_id', $request->user()->id)->findOrFail($id);
        $lembrete->update($dados);

        return response()->json($lembrete->fresh());
    }

    public function patch(Request $request, $id)
    {
        $dados = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'descricao' => 'sometimes|nullable|string',
            'categoria' => 'sometimes|nullable|string|max:80',
            'cor' => 'sometimes|nullable|string|max:20',
            'data_aviso' => 'sometimes|required|date',
            'data_lembrete' => 'sometimes|nullable|date',
            'status' => 'sometimes|required|in:pendente,concluido,cancelado',
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
    }
}
