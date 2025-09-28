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
            $table->string('booking_code')->unique();
            $table->uuid('room_id');
            $table->uuid('client_id');
            $table->uuid('rate_type_id');
            $table->uuid('currency_id');
            $table->datetime('check_in');
            $table->datetime('check_out');
            $table->integer('total_hours');
            $table->decimal('rate_per_unit', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->datetime('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('room_id')->references('id')->on('rooms');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('rate_type_id')->references('id')->on('rate_types');
            $table->foreign('currency_id')->references('id')->on('currencies');
            
            // Índices
            $table->index(['booking_code', 'deleted_at']);
            $table->index(['status', 'deleted_at']);
            $table->index(['check_in', 'check_out']);
            $table->index(['room_id', 'status']);
            $table->index('client_id');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
