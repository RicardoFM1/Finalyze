<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasTable('usuarios')) {
            Schema::rename('users', 'usuarios');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('usuarios') && !Schema::hasTable('users')) {
            Schema::rename('usuarios', 'users');
        }
    }
};
