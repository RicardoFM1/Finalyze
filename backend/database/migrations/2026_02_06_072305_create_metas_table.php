<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('titulo');
            $table->string('descricao')->nullable();
            $table->enum('tipo', ['financeira', 'pessoal'])->default('financeira');
            $table->decimal('valor_objetivo', 15, 2)->nullable();
            $table->decimal('valor_atual', 15, 2)->nullable()->default(0);
            $table->integer('meta_quantidade')->nullable();
            $table->integer('atual_quantidade')->nullable()->default(0);
            $table->string('unidade')->nullable();
            $table->date('prazo')->nullable();
            $table->enum('status', ['andamento', 'concluido', 'pausado', 'atrasado'])->default('andamento');
            $table->string('cor')->nullable();
            $table->string('icone')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metas');
    }
};
