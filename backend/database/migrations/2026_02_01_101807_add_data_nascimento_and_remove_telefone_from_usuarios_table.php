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
        Schema::table('usuarios', function (Blueprint $table) {
            $table->date('data_nascimento')->nullable()->after('cpf');
            $table->dropColumn('telefone');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('telefone')->nullable()->after('cpf');
            $table->dropColumn('data_nascimento');
        });
    }
};
