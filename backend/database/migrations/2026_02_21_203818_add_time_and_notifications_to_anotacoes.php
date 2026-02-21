<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anotacoes', function (Blueprint $table) {
            $table->time('hora')->nullable()->after('prazo');
            $table->boolean('notificacao_site')->default(false)->after('hora');
            $table->boolean('notificacao_email')->default(true)->after('notificacao_site');
        });
    }

    public function down(): void
    {
        Schema::table('anotacoes', function (Blueprint $table) {
            $table->dropColumn(['hora', 'notificacao_site', 'notificacao_email']);
        });
    }
};
