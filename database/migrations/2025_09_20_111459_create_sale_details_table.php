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
        Schema::create('sale_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sale_id');
            $table->uuid('product_id');
            
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2); // Precio unitario al momento de la venta
            $table->decimal('subtotal', 12, 2); // quantity * unit_price
            
            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('sale_id')
                  ->references('id')->on('sales')
                  ->cascadeOnDelete();
            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->cascadeOnDelete();
            
            // Índices
            $table->index(['sale_id', 'deleted_at']);
            $table->index(['product_id', 'deleted_at']);
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_details');
    }
};
