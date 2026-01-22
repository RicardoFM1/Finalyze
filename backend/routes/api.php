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
    Route::post('/checkout/preference', [App\Http\Controllers\CheckoutController::class, 'createPreference']);
    Route::post('/process_payment', [App\Http\Controllers\CheckoutController::class, 'processPayment']);

    // Plan Restricted Routes
    Route::middleware('has_plan')->group(function () {
        Route::get('/dashboard/summary', [App\Http\Controllers\DashboardController::class, 'summary']);

        // Transactions
        Route::apiResource('transactions', App\Http\Controllers\TransactionController::class);

        // Reports
        Route::get('/reports/monthly', [App\Http\Controllers\ReportController::class, 'monthly']);
    });

    // Plans Management (Admin)
    Route::middleware('admin')->group(function () {
        Route::post('/plans', [App\Http\Controllers\PlanController::class, 'store']);
        Route::put('/plans/{plan}', [App\Http\Controllers\PlanController::class, 'update']);
        Route::delete('/plans/{plan}', [App\Http\Controllers\PlanController::class, 'destroy']);
    });
});

// Pubic Webhook
Route::post('/webhook/mercadopago', [App\Http\Controllers\CheckoutController::class, 'handleWebhook']);
