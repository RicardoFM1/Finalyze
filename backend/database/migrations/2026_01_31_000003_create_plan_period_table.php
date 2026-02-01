<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plano_periodo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plano_id')->constrained('planos')->onDelete('cascade');
            $table->foreignId('periodo_id')->constrained('periodos')->onDelete('cascade');
            $table->integer('valor_centavos');
            $table->integer('percentual_desconto')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plano_periodo');
    }
};
