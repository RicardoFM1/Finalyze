<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('lembretes_enviados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assinatura_id')->constrained('assinaturas')->onDelete('cascade');
            $table->enum('tipo', ['3_dias', '1_dia'])->comment('Tipo de lembrete: 3 dias ou 1 dia antes da expiração');
            $table->timestamp('enviado_em');
            $table->timestamps();

            $table->unique(['assinatura_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lembretes_enviados');
    }
};
