<?php

namespace App\Servicos\Relatorios;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GerarRelatorioMensal
{
    public function executar(int $mesesCount = 6)
    {
        $workspaceId = app('workspace_id');
        $user = \App\Models\Usuario::findOrFail($workspaceId);

        $mesesCount = max(1, min(24, $mesesCount)); // Entre 1 e 24 meses
        $months = [];
        for ($i = $mesesCount - 1; $i >= 0; $i--) {
            $months[] = Carbon::now()->subMonths($i)->format('Y-m');
        }

        $cacheKey = "monthly_report_{$user->id}_{$mesesCount}";
        $startMonth = Carbon::createFromFormat('Y-m', $months[0])->startOfMonth();
        $endMonth = Carbon::createFromFormat('Y-m', $months[count($months) - 1])->endOfMonth();

        $cached = cache()->remember($cacheKey, now()->addMinutes(5), function () use ($user, $months, $startMonth, $endMonth) {
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
            $balanceData = [];
            foreach ($months as $month) {
                $inc = $totals[$month]['receita'] ?? 0.0;
                $exp = $totals[$month]['despesa'] ?? 0.0;
                $incomeData[] = $inc;
                $expenseData[] = $exp;
                $balanceData[] = $inc - $exp;
            }

            // Breakdown por categoria (Despesas) - No período selecionado
            $categories = $user->lancamentos()
                ->where('tipo', 'despesa')
                ->whereBetween('data', [$startMonth->toDateString(), $endMonth->toDateString()])
                ->selectRaw("categoria, SUM(valor) as total")
                ->groupBy('categoria')
                ->orderByDesc('total')
                ->get()
                ->map(fn($item) => [
                    'categoria' => $item->categoria,
                    'total' => (float) $item->total
                ]);

            return [
                'incomeData' => $incomeData,
                'expenseData' => $expenseData,
                'balanceData' => $balanceData,
                'categories' => $categories
            ];
        });

        $data = $cached;

        return [
            'labels' => array_map(fn($m) => Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y'), $months),
            'datasets' => [
                'income' => $data['incomeData'],
                'expense' => $data['expenseData'],
                'balance' => $data['balanceData']
            ],
            'categories' => $data['categories']
        ];
    }
}
