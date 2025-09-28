<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('auditable_type'); // Modelo afectado
            $table->uuid('auditable_id'); // ID del registro afectado
            $table->string('event'); // created, updated, deleted, restored
            $table->json('old_values')->nullable(); // Valores anteriores
            $table->json('new_values')->nullable(); // Valores nuevos
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->uuid('user_id')->nullable(); // Usuario que realizó la acción
            $table->timestamps();
            
            // Índices
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['user_id', 'created_at']);
            $table->index(['event', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
};
