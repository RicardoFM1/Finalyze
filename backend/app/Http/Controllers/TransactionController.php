<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        return Auth::user()->transactions()->latest()->get();
    }

    public function store(StoreTransactionRequest $request)
    {
        $user = Auth::user();
        
        // Quota check logic would go here
        // if ($user->transactions()->count() >= $user->plan->max_transactions) ...
        
        $validated = $request->validated();

        return $user->transactions()->create($validated);
    }
}
