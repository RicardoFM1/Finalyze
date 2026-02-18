<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Índices para Lancamentos
        Schema::table('lancamentos', function (Blueprint $table) {
            // Índice composto para listagem rápida por data do usuário
            $table->index(['user_id', 'data'], 'lancamentos_user_data_idx');
            // Índice para filtragem rápida por tipo (receita/despesa)
            $table->index(['user_id', 'tipo'], 'lancamentos_user_tipo_idx');
            // Índice para filtragem rápida por categoria
            $table->index(['user_id', 'categoria'], 'lancamentos_user_cat_idx');
        });

        // Índices para Metas
        Schema::table('metas', function (Blueprint $table) {
            // Índice para listagem por status (andamento, concluído)
            $table->index(['usuario_id', 'status'], 'metas_user_status_idx');
            $table->index(['usuario_id', 'tipo'], 'metas_user_tipo_idx');
        });

        // Índices para Anotacoes
        Schema::table('anotacoes', function (Blueprint $table) {
            $table->index(['usuario_id', 'status'], 'anotacoes_user_status_idx');
            // Útil para soft deletes
            $table->index(['usuario_id', 'deleted_at'], 'anotacoes_user_deleted_idx');
        });
    }

    public function down(): void
    {
        Schema::table('lancamentos', function (Blueprint $table) {
            $table->dropIndex('lancamentos_user_data_idx');
            $table->dropIndex('lancamentos_user_tipo_idx');
            $table->dropIndex('lancamentos_user_cat_idx');
        });

        Schema::table('metas', function (Blueprint $table) {
            $table->dropIndex('metas_user_status_idx');
            $table->dropIndex('metas_user_tipo_idx');
        });

        Schema::table('anotacoes', function (Blueprint $table) {
            $table->dropIndex('anotacoes_user_status_idx');
            $table->dropIndex('anotacoes_user_deleted_idx');
        });
    }
};
