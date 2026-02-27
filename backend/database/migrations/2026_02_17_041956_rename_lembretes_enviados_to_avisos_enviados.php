<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::rename('lembretes_enviados', 'avisos_enviados');
    }

    public function down(): void
    {
        Schema::rename('avisos_enviados', 'lembretes_enviados');
    }
};
