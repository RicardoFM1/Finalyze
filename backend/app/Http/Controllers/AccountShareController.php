<?php

namespace App\Http\Controllers;

use App\Models\AccountShare;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountShareController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Contas que eu compartilhei com outros
        $myShares = $user->shares()->with('guest')->get();

        // Contas que outros compartilharam comigo
        $sharedWithMe = AccountShare::where('guest_email', $user->email)
            ->with(['owner', 'guest'])
            ->get();

        return response()->json([
            'my_shares' => $myShares,
            'shared_with_me' => $sharedWithMe
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

        $share = AccountShare::updateOrCreate(
            ['owner_id' => $user->id, 'guest_email' => $request->email],
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
            'share' => $share
        ]);
    }

    public function destroy($id, Request $request)
    {
        $user = $request->user();
        $share = AccountShare::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                    ->orWhere('guest_email', $user->email);
            })
            ->firstOrFail();

        $share->delete();
        return response()->json(['message' => 'Compartilhamento removido.']);
    }
}
