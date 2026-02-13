<?php

namespace App\Http\Controllers;

use App\Models\ConviteAviso;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AvisoCompartilhamentoController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            ConviteAviso::where('usuario_id', $request->user()->id)
                ->latest()
                ->get()
        );
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'email_destino' => 'required|email|max:255',
            'mensagem' => 'nullable|string|max:1000',
        ]);

        $convite = ConviteAviso::create([
            'usuario_id' => $request->user()->id,
            'email_destino' => $dados['email_destino'],
            'mensagem' => $dados['mensagem'] ?? null,
            'status' => 'pendente',
        ]);

        return response()->json($convite, 201);
    }

    public function update(Request $request, $id)
    {
        $dados = $request->validate([
            'email_destino' => 'required|email|max:255',
            'mensagem' => 'nullable|string|max:1000',
            'status' => ['required', Rule::in(['pendente', 'aceito', 'recusado', 'cancelado'])],
        ]);

        $convite = ConviteAviso::where('usuario_id', $request->user()->id)->findOrFail($id);
        $convite->update($dados);

        return response()->json($convite->fresh());
    }

    public function patch(Request $request, $id)
    {
        $dados = $request->validate([
            'email_destino' => 'sometimes|required|email|max:255',
            'mensagem' => 'sometimes|nullable|string|max:1000',
            'status' => ['sometimes|required', Rule::in(['pendente', 'aceito', 'recusado', 'cancelado'])],
        ]);

        $convite = ConviteAviso::where('usuario_id', $request->user()->id)->findOrFail($id);
        $convite->update($dados);

        return response()->json($convite->fresh());
    }

    public function destroy(Request $request, $id)
    {
        $convite = ConviteAviso::where('usuario_id', $request->user()->id)->findOrFail($id);
        $convite->delete();

        return response()->json(null, 204);
    }
}
