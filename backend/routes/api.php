<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/auth/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');


Route::get('/plans', [App\Http\Controllers\PlanController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [App\Http\Controllers\UserController::class, 'show']);
    Route::put('/user', [App\Http\Controllers\UserController::class, 'update']);
    Route::get('/checkout/preference', [App\Http\Controllers\CheckoutController::class, 'getLatestPreference']);
    Route::post('/checkout/preference', [App\Http\Controllers\CheckoutController::class, 'createPreference']);
    Route::post('/checkout/process_payment', [App\Http\Controllers\CheckoutController::class, 'processPayment']);



    Route::middleware('has_plan')->group(function () {
        Route::get('/dashboard/summary', [App\Http\Controllers\DashboardController::class, 'summary']);


        Route::apiResource('transactions', App\Http\Controllers\TransactionController::class);


        Route::get('/reports/monthly', [App\Http\Controllers\ReportController::class, 'monthly']);
    });


    Route::middleware('admin')->group(function () {
        Route::get('/admin/plans', [App\Http\Controllers\PlanController::class, 'adminIndex']);
        Route::post('/plans', [App\Http\Controllers\PlanController::class, 'store']);
        Route::put('/plans/{plan}', [App\Http\Controllers\PlanController::class, 'update']);
        Route::delete('/plans/{plan}', [App\Http\Controllers\PlanController::class, 'destroy']);
    });
});


Route::any('/webhook/mercadopago', [App\Http\Controllers\CheckoutController::class, 'handleWebhook']);

Route::fallback(function () {
    return response()->json([
        'message' => 'Rota não encontrada'
    ], 404);
});
