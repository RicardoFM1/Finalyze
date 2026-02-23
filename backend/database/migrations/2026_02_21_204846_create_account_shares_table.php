<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('guest_email');
            $table->enum('status', ['pending', 'accepted'])->default('pending');
            $table->timestamps();

            $table->unique(['owner_id', 'guest_email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_shares');
    }
};
