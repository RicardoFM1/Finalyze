<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        
        Schema::table('lancamentos', function (Blueprint $table) {
            
            $table->index(['user_id', 'data'], 'lancamentos_user_data_idx');
            
            $table->index(['user_id', 'tipo'], 'lancamentos_user_tipo_idx');
            
            $table->index(['user_id', 'categoria'], 'lancamentos_user_cat_idx');
        });

        Schema::table('metas', function (Blueprint $table) {
            
            $table->index(['usuario_id', 'status'], 'metas_user_status_idx');
            $table->index(['usuario_id', 'tipo'], 'metas_user_tipo_idx');
        });

        Schema::table('anotacoes', function (Blueprint $table) {
            $table->index(['usuario_id', 'status'], 'anotacoes_user_status_idx');
            
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
