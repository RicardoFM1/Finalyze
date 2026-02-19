<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('convites_enviados', function (Blueprint $table) {
            $table->string('token_hash', 64)->nullable()->unique()->after('status');
            $table->timestamp('expira_em')->nullable()->after('token_hash');
            $table->timestamp('aceito_em')->nullable()->after('expira_em');
        });
    }

    public function down(): void
    {
        Schema::table('convites_enviados', function (Blueprint $table) {
            $table->dropColumn(['token_hash', 'expira_em', 'aceito_em']);
        });
    }
};

