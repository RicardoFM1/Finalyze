<?php

namespace App\Http\Controllers;

use App\Servicos\Relatorios\GerarRelatorioMensal;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function mensal(Request $request, GerarRelatorioMensal $servico)
    {
        return response()->json($servico->executar((int)($request->meses ?? 6)));
    }
}
