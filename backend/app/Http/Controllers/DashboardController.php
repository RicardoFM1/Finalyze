<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function resumo(Request $request, \App\Servicos\Painel\GerarResumoPainel $servico)
    {
        return response()->json($servico->executar());
    }
}
