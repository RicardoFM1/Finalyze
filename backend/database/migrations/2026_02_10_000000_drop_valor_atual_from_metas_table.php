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
            if (Schema::hasColumn('metas', 'valor_atual')) {
                $table->dropColumn('valor_atual');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('metas', function (Blueprint $table) {
            $table->decimal('valor_atual', 15, 2)->default(0)->after('valor_objetivo');
        });
    }
};
