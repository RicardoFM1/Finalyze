<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('lembretes', function (Blueprint $table) {
            $table->timestamp('email_notified_at')->nullable();
            $table->timestamp('site_notified_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('lembretes', function (Blueprint $table) {
            $table->dropColumn(['email_notified_at', 'site_notified_at']);
        });
    }
};
