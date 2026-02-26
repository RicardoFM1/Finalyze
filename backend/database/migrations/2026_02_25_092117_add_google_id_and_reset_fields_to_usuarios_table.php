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
            $table->string('google_id')->nullable()->unique()->after('email');
            $table->string('senha')->nullable()->change();
            $table->string('reset_code')->nullable()->after('codigo_expira_em');
            $table->timestamp('reset_code_expires_at')->nullable()->after('reset_code');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('senha')->nullable(false)->change();
            $table->dropColumn(['google_id', 'reset_code', 'reset_code_expires_at']);
        });
    }
};
