<?php

namespace App\Servicos\Relatorios;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GerarRelatorioMensal
{
    public function executar()
    {
        $user = Auth::user();

        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[] = Carbon::now()->subMonths($i)->format('Y-m');
        }

        $incomeData = [];
        $expenseData = [];

        foreach ($months as $month) {
            $income = $user->lancamentos()
                ->where('tipo', 'receita')
                ->where(DB::raw("TO_CHAR(data, 'YYYY-MM')"), $month)
                ->sum('valor');

            $expense = $user->lancamentos()
                ->where('tipo', 'despesa')
                ->where(DB::raw("TO_CHAR(data, 'YYYY-MM')"), $month)
                ->sum('valor');

            $incomeData[] = $income;
            $expenseData[] = $expense;
        }

        return [
            'labels' => array_map(fn($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'), $months),
            'datasets' => [
                [
                    'label' => 'Receitas',
                    'backgroundColor' => '#4CAF50',
                    'data' => $incomeData
                ],
                [
                    'label' => 'Despesas',
                    'backgroundColor' => '#F44336',
                    'data' => $expenseData
                ]
            ]
        ];
    }
}
