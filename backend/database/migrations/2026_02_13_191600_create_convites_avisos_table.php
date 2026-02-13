<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('convites_avisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('email_destino');
            $table->text('mensagem')->nullable();
            $table->enum('status', ['pendente', 'aceito', 'recusado', 'cancelado'])->default('pendente');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['usuario_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('convites_avisos');
    }
};
