<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('payment_code')->unique();
            $table->uuid('booking_id');
            $table->uuid('currency_id');
            $table->decimal('amount', 10, 2);
            $table->decimal('exchange_rate', 12, 6)->default(1.000000);
            $table->decimal('amount_base_currency', 10, 2); // Monto en moneda base
            $table->enum('payment_method', ['cash', 'card', 'debit_card', 'credit_card', 'transfer', 'yape', 'plin', 'check', 'other']);
            $table->string('reference')->nullable();
            $table->datetime('payment_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled', 'refunded'])->default('completed');
            
            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('booking_id')->references('id')->on('bookings');
            $table->foreign('currency_id')->references('id')->on('currencies');
            
            // Índices
            $table->index(['booking_id', 'status', 'deleted_at']);
            $table->index(['payment_date', 'deleted_at']);
            $table->index('payment_code');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};