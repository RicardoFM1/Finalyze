<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historico_pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('assinatura_id')->constrained('assinaturas')->onDelete('cascade');
            $table->integer('valor_centavos');
            $table->string('status'); // paid, pending, failed
            $table->string('metodo_pagamento')->nullable();
            $table->string('mercado_pago_id')->nullable();
            $table->timestamp('pago_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historico_pagamentos');
    }
};
