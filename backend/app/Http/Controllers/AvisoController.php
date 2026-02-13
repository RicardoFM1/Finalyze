<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use Illuminate\Http\Request;

class AvisoController extends Controller
{
    public function index(Request $request)
    {
        $query = Aviso::where('usuario_id', $request->user()->id);

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
            'status' => 'nullable|in:pendente,concluido,cancelado',
        ]);

        $dados['usuario_id'] = $request->user()->id;
        $dados['status'] = $dados['status'] ?? 'pendente';

        $aviso = Aviso::create($dados);

        return response()->json($aviso, 201);
    }

    public function update(Request $request, $id)
    {
        $dados = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'categoria' => 'nullable|string|max:80',
            'cor' => 'nullable|string|max:20',
            'data_aviso' => 'required|date',
            'status' => 'required|in:pendente,concluido,cancelado',
        ]);

        $aviso = Aviso::where('usuario_id', $request->user()->id)->findOrFail($id);
        $aviso->update($dados);

        return response()->json($aviso->fresh());
    }

    public function patch(Request $request, $id)
    {
        $dados = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'descricao' => 'sometimes|nullable|string',
            'categoria' => 'sometimes|nullable|string|max:80',
            'cor' => 'sometimes|nullable|string|max:20',
            'data_aviso' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:pendente,concluido,cancelado',
        ]);

        $aviso = Aviso::where('usuario_id', $request->user()->id)->findOrFail($id);
        $aviso->update($dados);

        return response()->json($aviso->fresh());
    }

    public function patchStatus(Request $request, $id)
    {
        $dados = $request->validate([
            'status' => 'required|in:pendente,concluido,cancelado',
        ]);

        $aviso = Aviso::where('usuario_id', $request->user()->id)->findOrFail($id);
        $aviso->update(['status' => $dados['status']]);

        return response()->json($aviso->fresh());
    }

    public function destroy(Request $request, $id)
    {
        $aviso = Aviso::where('usuario_id', $request->user()->id)->findOrFail($id);
        $aviso->delete();

        return response()->json(null, 204);
    }
}
