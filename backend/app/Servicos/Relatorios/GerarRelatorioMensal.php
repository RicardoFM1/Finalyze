<?php

namespace App\Servicos\Relatorios;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GerarRelatorioMensal
{
    public function executar(int $mesesCount = 6)
    {
        $user = Auth::user();

        $mesesCount = max(1, min(24, $mesesCount)); // Entre 1 e 24 meses
        $months = [];
        for ($i = $mesesCount - 1; $i >= 0; $i--) {
            $months[] = Carbon::now()->subMonths($i)->format('Y-m');
        }

        $cacheKey = "monthly_report_{$user->id}_{$mesesCount}";
        $cached = cache()->remember($cacheKey, now()->addMinutes(5), function () use ($user, $months) {
            $startMonth = Carbon::createFromFormat('Y-m', $months[0])->startOfMonth();
            $endMonth = Carbon::createFromFormat('Y-m', $months[count($months) - 1])->endOfMonth();

            $rows = $user->lancamentos()
                ->selectRaw("TO_CHAR(data, 'YYYY-MM') as month_key, tipo, SUM(valor) as total")
                ->whereBetween('data', [$startMonth->toDateString(), $endMonth->toDateString()])
                ->whereIn('tipo', ['receita', 'despesa'])
                ->groupBy('month_key', 'tipo')
                ->get();

            $totals = [];
            foreach ($rows as $row) {
                $totals[$row->month_key][$row->tipo] = (float) $row->total;
            }

            $incomeData = [];
            $expenseData = [];
            foreach ($months as $month) {
                $incomeData[] = $totals[$month]['receita'] ?? 0.0;
                $expenseData[] = $totals[$month]['despesa'] ?? 0.0;
            }

            return [$incomeData, $expenseData];
        });

        [$incomeData, $expenseData] = $cached;

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
