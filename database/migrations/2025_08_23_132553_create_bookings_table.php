<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('booking_code')->unique(); // Ej: HAB-2024-0001
            
            // RELACIONES PRINCIPALES
            $table->uuid('room_id'); // La habitación asignada
            $table->uuid('customers_id'); // El cliente a nombre de quien está
            $table->uuid('rate_type_id'); // Tipo de tarifa (por hora, por noche, etc)
            $table->uuid('currency_id');
            $table->uuid('sub_branch_id');
            // TIEMPO Y FECHAS
            $table->datetime('check_in'); // Cuando entró
            $table->datetime('check_out'); // Cuando debe salir (check_in + horas contratadas)
            $table->datetime('actual_check_out')->nullable(); // Cuando realmente salió
            $table->integer('total_hours'); // Horas contratadas (2, 4, 8, etc)
            $table->integer('actual_hours')->nullable(); // Horas reales que estuvo
            
            // COSTOS
            $table->decimal('rate_per_hour', 10, 2); // Precio por hora de la habitación
            $table->decimal('room_subtotal', 10, 2); // Solo costo de habitación
            $table->decimal('products_subtotal', 10, 2)->default(0); // Total productos consumidos
            $table->decimal('tax_amount', 10, 2)->default(0); // IGV
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2); // Gran total (habitación + productos)
            $table->decimal('paid_amount', 10, 2)->default(0);
            
            // ESTADOS
            $table->enum('status', [
                'active',       // Cliente está en la habitación AHORA
                'finished',     // Ya terminó (manual o automático)
                'cancelled'     // Cancelado
            ])->default('active');
            
            // CÓMO TERMINÓ
            $table->enum('finish_type', [
                'manual',       // Lo finalizaste tú
                'automatic',    // Se acabó el tiempo automáticamente
            ])->nullable();
            
            // TIPO DE COMPROBANTE
            $table->enum('voucher_type', [
                'ticket',       // Ticket simple
                'boleta',       // Boleta de venta
                'factura'       // Factura
            ])->default('ticket');
            
            $table->string('voucher_number')->nullable(); // Serie y correlativo
            
            $table->text('notes')->nullable();
            $table->integer('updated_by')->nullable()->after('paid_amount');
            // AUDITORÍA
            $table->unsignedBigInteger('created_by')->nullable(); // Quien registró la entrada
            $table->unsignedBigInteger('finished_by')->nullable(); // Quien finalizó
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('room_id')->references('id')->on('rooms');
            $table->foreign('customers_id')->references('id')->on('customers');
            $table->foreign('rate_type_id')->references('id')->on('rate_types');
            $table->foreign('currency_id')->references('id')->on('currencies');
            
            // Índices importantes
            $table->index(['room_id', 'status']);
            $table->index(['status', 'check_out']);
            $table->index(['booking_code']);
            $table->index(['customers_id']);
            $table->index(['created_at', 'status']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};