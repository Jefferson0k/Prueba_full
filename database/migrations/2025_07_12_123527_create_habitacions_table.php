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
        Schema::create('habitaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('piso_id')->constrained('pisos')->onDelete('cascade');
            $table->foreignId('tipo_id')->constrained('tipos_habitacion')->onDelete('cascade');
            $table->enum('estado', ['libre', 'ocupado', 'limpieza', 'mantenimiento'])->default('libre');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habitacions');
    }
};
