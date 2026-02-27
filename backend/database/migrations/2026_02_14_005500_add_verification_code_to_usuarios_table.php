<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $coluna) {
            $coluna->string('codigo_verificacao')->nullable()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $coluna) {
            $coluna->dropColumn('codigo_verificacao');
        });
    }
};
