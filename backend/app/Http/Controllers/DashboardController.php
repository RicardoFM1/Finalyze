<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        $user = Auth::user();
        
        // Mock data or real db query
        $income = $user->transactions()->where('type', 'income')->sum('amount');
        $expense = $user->transactions()->where('type', 'expense')->sum('amount');
        $balance = $income - $expense;
        
        $recent = $user->transactions()->latest()->take(5)->get();

        return response()->json([
            'income' => $income,
            'expense' => $expense,
            'balance' => $balance,
            'recent_activity' => $recent
        ]);
    }
}
