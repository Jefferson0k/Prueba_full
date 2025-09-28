<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->uuid('product_id');
            $table->string('movement_code')->unique();
            $table->enum('movement_type', ['entry', 'exit', 'adjustment', 'transfer']);
            $table->integer('quantity');
            $table->integer('previous_stock');
            $table->integer('current_stock');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->text('reason')->nullable();
            $table->string('reference_document')->nullable();
            $table->uuid('booking_id')->nullable();
            $table->uuid('transfer_to_branch_id')->nullable(); // Para transferencias
            
            // Auditoría
            $table->uuid('created_by')->nullable();
            $table->uuid('approved_by')->nullable();
            $table->datetime('approved_at')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('booking_id')->references('id')->on('bookings');
            $table->foreign('transfer_to_branch_id')->references('id')->on('branches');
            
            // Índices
            $table->index(['branch_id', 'product_id', 'created_at']);
            $table->index(['movement_type', 'created_at']);
            $table->index('booking_id');
            $table->index('created_by');
            $table->index('approved_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_movements');
    }
};

