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
        Schema::rename('account_shares', 'colaboracoes');

        Schema::table('colaboracoes', function (Blueprint $table) {
            $table->renameColumn('owner_id', 'proprietario_id');
            $table->renameColumn('guest_email', 'email_convidado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colaboracoes', function (Blueprint $table) {
            $table->renameColumn('proprietario_id', 'owner_id');
            $table->renameColumn('email_convidado', 'guest_email');
        });

        Schema::rename('colaboracoes', 'account_shares');
    }
};
