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
        Schema::create('usos_habitacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habitacion_id')->constrained('habitaciones')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('horario_id')->constrained('horarios')->onDelete('cascade');
            $table->enum('tipo_uso', ['hora', 'noche', 'día completo']);
            $table->integer('cantidad_horas')->default(1);
            $table->datetime('hora_entrada');
            $table->datetime('hora_salida');
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uso_habitacions');
    }
};
