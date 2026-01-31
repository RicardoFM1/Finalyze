<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('planos', function (Blueprint $table) {
            // Check if columns exist before dropping to avoid errors during rollback/retry
            if (Schema::hasColumn('planos', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('planos', 'price_cents')) {
                $table->dropColumn('price_cents');
            }
            if (Schema::hasColumn('planos', 'interval')) {
                $table->dropColumn('interval');
            }
            if (Schema::hasColumn('planos', 'features')) {
                $table->dropColumn('features');
            }
        });
    }

    public function down(): void
    {
        Schema::table('planos', function (Blueprint $table) {
            $table->integer('price_cents')->nullable();
            $table->string('interval')->nullable();
            $table->json('features')->nullable();
        });
    }
};
