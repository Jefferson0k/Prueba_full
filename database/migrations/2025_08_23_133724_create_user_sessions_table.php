<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('session_token')->unique();
            $table->string('ip_address', 45);
            $table->string('user_agent');
            $table->datetime('login_at');
            $table->datetime('logout_at')->nullable();
            $table->datetime('last_activity');
            $table->enum('status', ['active', 'expired', 'terminated'])->default('active');
            $table->timestamps();
            
            // Índices
            $table->index(['user_id', 'status']);
            $table->index(['session_token', 'status']);
            $table->index('last_activity');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_sessions');
    }
};
