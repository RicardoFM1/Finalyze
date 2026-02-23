<?php

namespace App\Http\Controllers;

use App\Mail\ConviteCompartilhamento;
use App\Models\ConviteEnviado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AvisoCompartilhamentoController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            ConviteEnviado::where('usuario_id', $request->user()->id)
                ->latest()
                ->get()
        );
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'email_destino' => ['required', 'email', 'max:255', Rule::exists('usuarios', 'email')],
            'mensagem'      => 'nullable|string|max:1000',
        ], [
            'email_destino.exists' => 'O email destino precisa estar cadastrado no Finalyze.',
        ]);

        if (strtolower($dados['email_destino']) === strtolower($request->user()->email)) {
            return response()->json([
                'message' => 'Nao e possivel enviar convite para o proprio email.',
            ], 422);
        }

        $tokenRaw  = Str::random(64);
        $tokenHash = hash('sha256', $tokenRaw);
        $expiraEm  = now()->addHours(48);

        $convite = ConviteEnviado::create([
            'usuario_id'    => $request->user()->id,
            'email_destino' => $dados['email_destino'],
            'mensagem'      => $dados['mensagem'] ?? null,
            'status'        => 'pendente',
            'token_hash'    => $tokenHash,
            'expira_em'     => $expiraEm,
            'aceito_em'     => null,
        ]);

        $frontendUrl = rtrim((string) env('APP_FRONTEND_URL', config('app.url')), '/');
        $linkConvite = $frontendUrl . '/#/convites/aceitar?token=' . urlencode($tokenRaw);

        Mail::to($dados['email_destino'])->send(
            new ConviteCompartilhamento($request->user(), $convite, $linkConvite)
        );

        return response()->json($convite, 201);
    }

    public function aceitarToken(Request $request)
    {
        $dados = $request->validate([
            'token' => 'required|string|min:32',
        ]);

        $tokenHash = hash('sha256', $dados['token']);

        $convite = ConviteEnviado::where('token_hash', $tokenHash)
            ->where('status', 'pendente')
            ->whereNotNull('expira_em')
            ->where('expira_em', '>', now())
            ->first();

        if (!$convite) {
            return response()->json([
                'message' => 'Convite invalido ou expirado.',
            ], 404);
        }

        if (strtolower($request->user()->email) !== strtolower($convite->email_destino)) {
            return response()->json([
                'message' => 'Este convite pertence a outro email.',
            ], 403);
        }

        $convite->update([
            'status'    => 'aceito',
            'aceito_em' => now(),
        ]);

        return response()->json([
            'message'           => 'Convite aceito com sucesso.',
            'convite_id'        => $convite->id,
            'usuario_origem_id' => $convite->usuario_id,
        ]);
    }

    public function update(Request $request, $id)
    {
        $dados = $request->validate([
            'email_destino' => 'required|email|max:255',
            'mensagem'      => 'nullable|string|max:1000',
            'status'        => ['required', Rule::in(['pendente', 'aceito', 'recusado', 'cancelado'])],
        ]);

        $convite = ConviteEnviado::where('usuario_id', $request->user()->id)->findOrFail($id);
        $convite->update($dados);

        return response()->json($convite->fresh());
    }

    public function patch(Request $request, $id)
    {
        $dados = $request->validate([
            'email_destino' => 'sometimes|required|email|max:255',
            'mensagem'      => 'sometimes|nullable|string|max:1000',
            'status'        => ['sometimes', 'required', Rule::in(['pendente', 'aceito', 'recusado', 'cancelado'])],
        ]);

        $convite = ConviteEnviado::where('usuario_id', $request->user()->id)->findOrFail($id);
        $convite->update($dados);

        return response()->json($convite->fresh());
    }

    public function destroy(Request $request, $id)
    {
        $convite = ConviteEnviado::where('usuario_id', $request->user()->id)->findOrFail($id);
        $convite->delete();

        return response()->json(null, 204);
    }
}
