<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        $usuario = Auth::user();

        $receita = $usuario->lancamentos()->where('tipo', 'receita')->sum('valor');
        $despesa = $usuario->lancamentos()->where('tipo', 'despesa')->sum('valor');
        $saldo = $receita - $despesa;

        $recentes = $usuario->lancamentos()->latest()->take(5)->get();

        return response()->json([
            'receita' => $receita,
            'despesa' => $despesa,
            'saldo' => $saldo,
            'atividades_recentes' => $recentes
        ]);
    }
}
