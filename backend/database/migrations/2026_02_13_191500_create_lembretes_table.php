<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lembretes')) {
            Schema::create('lembretes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
                $table->string('titulo');
                $table->text('descricao')->nullable();
                $table->string('categoria', 80)->nullable();
                $table->string('cor', 20)->nullable();
                $table->dateTime('data_aviso');
                $table->enum('status', ['pendente', 'concluido', 'cancelado'])->default('pendente');
                $table->softDeletes();
                $table->timestamps();

                $table->index(['usuario_id', 'data_aviso']);
            });
            return;
        }

        // Legacy databases may already have the table but without this column.
        if (!Schema::hasColumn('lembretes', 'data_aviso')) {
            Schema::table('lembretes', function (Blueprint $table) {
                $table->dateTime('data_aviso')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lembretes');
    }
};
