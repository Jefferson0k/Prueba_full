<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos_personal', function (Blueprint $table) {
            $table->id();
            
            // Relación con el usuario (personal)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            
            // Relación con sub_branch (sucursal)
            $table->uuid('sub_branch_id');
            $table->foreign('sub_branch_id')
                  ->references('id')
                  ->on('sub_branches')
                  ->cascadeOnDelete();
            
            // Información del pago
            $table->decimal('monto', 10, 2);
            $table->date('fecha_pago');
            $table->string('periodo'); // Ej: "Enero 2024", "Semana 1 - Octubre 2024"
            $table->enum('tipo_pago', ['salario', 'adelanto', 'bonificacion', 'comision', 'otro'])
                  ->default('salario');
            
            // Método de pago
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'cheque'])
                  ->default('efectivo');
            
            // Información adicional
            $table->text('concepto')->nullable();
            $table->string('comprobante')->nullable(); // Ruta del archivo PDF/imagen
            $table->enum('estado', ['pendiente', 'pagado', 'anulado'])
                  ->default('pendiente');
            
            // Usuario que registró el pago
            $table->foreignId('registrado_por')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            
            $table->timestamps();
            
            // Índices para mejorar búsquedas
            $table->index(['sub_branch_id', 'fecha_pago']);
            $table->index(['user_id', 'fecha_pago']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_personal');
    }
};