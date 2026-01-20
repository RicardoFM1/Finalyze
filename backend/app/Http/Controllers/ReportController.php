<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function monthly(Request $request)
    {
        $user = Auth::user();
        
        // Get last 6 months
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[] = Carbon::now()->subMonths($i)->format('Y-m');
        }

        $incomeData = [];
        $expenseData = [];

        foreach ($months as $month) {
            $income = $user->transactions()
                ->where('type', 'income')
                ->where(DB::raw("TO_CHAR(date, 'YYYY-MM')"), $month)
                ->sum('amount');
            
            $expense = $user->transactions()
                ->where('type', 'expense')
                ->where(DB::raw("TO_CHAR(date, 'YYYY-MM')"), $month)
                ->sum('amount');

            $incomeData[] = $income;
            $expenseData[] = $expense;
        }

        return response()->json([
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
        ]);
    }
}
