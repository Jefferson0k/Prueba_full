<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('room_status_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('room_id');
            $table->string('previous_status');
            $table->string('new_status');
            $table->text('reason')->nullable();
            $table->datetime('changed_at');
            $table->uuid('changed_by');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('room_id')->references('id')->on('rooms');
            
            // Índices
            $table->index(['room_id', 'changed_at']);
            $table->index(['new_status', 'changed_at']);
            $table->index('changed_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_status_logs');
    }
};