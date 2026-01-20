<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        return Auth::user()->transactions()->latest()->get();
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Quota check logic would go here
        // if ($user->transactions()->count() >= $user->plan->max_transactions) ...
        
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric',
            'category' => 'required|string',
            'date' => 'required|date',
            'description' => 'nullable|string'
        ]);

        return $user->transactions()->create($validated);
    }
}
