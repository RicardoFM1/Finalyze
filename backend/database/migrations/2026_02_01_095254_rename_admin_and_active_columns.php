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
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('eh_admin', 'admin');
        });

        Schema::table('planos', function (Blueprint $table) {
            $table->renameColumn('esta_ativo', 'ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('admin', 'eh_admin');
        });

        Schema::table('planos', function (Blueprint $table) {
            $table->renameColumn('ativo', 'esta_ativo');
        });
    }
};
