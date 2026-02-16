<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});

Route::post('/auth/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/auth/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::post('/auth/verificar', [App\Http\Controllers\AuthController::class, 'verificarCodigo']);
Route::post('/auth/reenviar', [App\Http\Controllers\AuthController::class, 'reenviarCodigo']);


Route::get('/planos', [App\Http\Controllers\PlanController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/usuario', [App\Http\Controllers\UserController::class, 'mostrar']);
    Route::put('/usuario', [App\Http\Controllers\UserController::class, 'atualizar']);
    Route::delete('/usuario/avatar', [App\Http\Controllers\UserController::class, 'removerAvatar']);
    Route::get('/checkout/preferencia', [App\Http\Controllers\CheckoutController::class, 'pegarUltimaPreferencia']);
    Route::post('/checkout/preferencia', [App\Http\Controllers\CheckoutController::class, 'criarPreferencia']);
    Route::post('/checkout/processar_pagamento', [App\Http\Controllers\CheckoutController::class, 'processarPagamento']);
    Route::get('/checkout/status/{id}', [App\Http\Controllers\CheckoutController::class, 'checarStatusPagamento']);
    Route::match(['put', 'post'], '/checkout/cancelar_pagamento', [App\Http\Controllers\CheckoutController::class, 'cancelarPagamento']);

    Route::get('/assinaturas', [App\Http\Controllers\SubscriptionController::class, 'index']);
    Route::post('/assinaturas/ligar-auto-renovacao', [App\Http\Controllers\SubscriptionController::class, 'ativarAutoRenovacao']);
    Route::post('/assinaturas/cancelar', [App\Http\Controllers\SubscriptionController::class, 'cancelar']);
    Route::post('/assinaturas/auto-renovacao', [App\Http\Controllers\SubscriptionController::class, 'toggleAutoRenewal']);



    Route::middleware('has_plan')->group(function () {
        Route::get('/painel/resumo', [App\Http\Controllers\DashboardController::class, 'resumo']);


        Route::get('/lancamentos', [App\Http\Controllers\LancamentoController::class, 'mostrar']);
        Route::post('/lancamentos', [App\Http\Controllers\LancamentoController::class, 'criar']);
        Route::put('/lancamentos/{lancamentoId}', [App\Http\Controllers\LancamentoController::class, 'editar']);
        Route::delete('/lancamentos/{lancamentoId}', [App\Http\Controllers\LancamentoController::class, 'deletar']);


        Route::get('/relatorios/mensal', [App\Http\Controllers\ReportController::class, 'mensal']);

        Route::middleware('check_resource:metas')->group(function () {
            Route::get('/metas', [App\Http\Controllers\MetaController::class, 'index']);
            Route::post('/metas', [App\Http\Controllers\MetaController::class, 'store']);
            Route::put('/metas/{id}', [App\Http\Controllers\MetaController::class, 'update']);
            Route::delete('/metas/{id}', [App\Http\Controllers\MetaController::class, 'destroy']);
            Route::post('/metas/{id}/reativar', [App\Http\Controllers\MetaController::class, 'reativar']);

            Route::get('/anotacoes', [App\Http\Controllers\AnotacaoController::class, 'index']);
            Route::post('/anotacoes', [App\Http\Controllers\AnotacaoController::class, 'store']);
            Route::put('/anotacoes/{id}', [App\Http\Controllers\AnotacaoController::class, 'update']);
            Route::delete('/anotacoes/{id}', [App\Http\Controllers\AnotacaoController::class, 'destroy']);
            Route::post('/anotacoes/{id}/reativar', [App\Http\Controllers\AnotacaoController::class, 'reativar']);
        });
    });


    Route::middleware('admin')->group(function () {
        Route::get('/admin/planos', [App\Http\Controllers\PlanController::class, 'adminIndex']);
        Route::get('/admin/periodos', function () {
            return \App\Models\Periodo::all();
        });
        Route::get('/admin/recursos', function () {
            return \App\Models\Recurso::all();
        });
        Route::post('/planos', [App\Http\Controllers\PlanController::class, 'criar']);
        Route::put('/planos/{plano}', [App\Http\Controllers\PlanController::class, 'atualizar']);
        Route::delete('/planos/{plano}', [App\Http\Controllers\PlanController::class, 'destruir']);
    });

    Route::middleware('check_resource:finn-ai')->group(function () {
        Route::post('/chat/pergunta', [App\Http\Controllers\ChatIaController::class, 'perguntar']);
    });
});


Route::any('/webhook/mercadopago', [App\Http\Controllers\CheckoutController::class, 'handleWebhook']);

Route::fallback(function () {
    return response()->json([
        'message' => 'Rota não encontrada'
    ], 404);
});
