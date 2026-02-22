<?php

namespace App\Http\Controllers;

use App\Models\Colaboracao;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ColaboracaoController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Contas que eu compartilhei com outros
        $minhasColaboracoes = $user->colaboracoes()->with('guest')->get();

        // Contas que outros compartilharam comigo
        $colaboracoesComigo = Colaboracao::where('email_convidado', $user->email)
            ->with(['proprietario.plano.recursos', 'guest'])
            ->get();

        return response()->json([
            'my_shares' => $minhasColaboracoes,
            'shared_with_me' => $colaboracoesComigo
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = $request->user();

        if ($user->email === $request->email) {
            return response()->json(['message' => 'Você não pode compartilhar com você mesmo.'], 422);
        }

        $colaboracao = Colaboracao::updateOrCreate(
            ['proprietario_id' => $user->id, 'email_convidado' => $request->email],
            ['status' => 'pending']
        );

        try {
            \Illuminate\Support\Facades\Mail::to($request->email)
                ->locale(app()->getLocale())
                ->send(new \App\Mail\InviteCollaboration($user));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erro ao enviar e-mail de convite: " . $e->getMessage());
        }

        return response()->json([
            'message' => 'Convite enviado com sucesso!',
            'share' => $colaboracao
        ]);
    }

    public function destroy($id, Request $request)
    {
        $user = $request->user();
        $colaboracao = Colaboracao::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('proprietario_id', $user->id)
                    ->orWhere('email_convidado', $user->email);
            })
            ->firstOrFail();

        $colaboracao->delete();
        return response()->json(['message' => 'Compartilhamento removido.']);
    }
}
