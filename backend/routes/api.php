<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/auth/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');

// Public Plans
Route::get('/plans', [App\Http\Controllers\PlanController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [App\Http\Controllers\UserController::class, 'show']);
    Route::put('/user', [App\Http\Controllers\UserController::class, 'update']);
    Route::get('/dashboard/summary', [App\Http\Controllers\DashboardController::class, 'summary']);
    Route::post('/checkout/preference', [App\Http\Controllers\CheckoutController::class, 'createPreference']);
    
    // Plans Management (Admin)
    Route::post('/plans', [App\Http\Controllers\PlanController::class, 'store']);
    Route::put('/plans/{plan}', [App\Http\Controllers\PlanController::class, 'update']);
    Route::delete('/plans/{plan}', [App\Http\Controllers\PlanController::class, 'destroy']);
    
    // Transactions
    Route::apiResource('transactions', App\Http\Controllers\TransactionController::class);
    
    // Reports
    Route::get('/reports/monthly', [App\Http\Controllers\ReportController::class, 'monthly']);
});
