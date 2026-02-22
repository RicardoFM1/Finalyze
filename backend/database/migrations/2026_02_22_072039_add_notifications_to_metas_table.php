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
        Schema::table('metas', function (Blueprint $table) {
            $table->boolean('notificacao_site')->default(false)->after('prazo');
            $table->boolean('notificacao_email')->default(true)->after('notificacao_site');
            $table->timestamp('email_notified_at')->nullable()->after('notificacao_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('metas', function (Blueprint $table) {
            $table->dropColumn(['notificacao_site', 'notificacao_email', 'email_notified_at']);
        });
    }
};
