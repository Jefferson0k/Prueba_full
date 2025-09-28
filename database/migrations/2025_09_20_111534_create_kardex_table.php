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
        Schema::create('kardex', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sub_branch_id');
            $table->uuid('product_id');
            
            $table->enum('movement_type', ['entry', 'exit', 'adjustment', 'transfer']);
            $table->string('reference_type')->nullable(); // 'sale', 'purchase', 'adjustment', 'transfer'
            $table->uuid('reference_id')->nullable(); // ID de la venta, compra, etc.
            
            $table->date('movement_date');
            $table->integer('quantity'); // Positivo para entradas, negativo para salidas
            $table->decimal('unit_cost', 10, 2)->nullable(); // Costo unitario
            $table->integer('stock_before'); // Stock antes del movimiento
            $table->integer('stock_after');  // Stock después del movimiento
            
            $table->text('description')->nullable();
            
            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('sub_branch_id')
                  ->references('id')->on('sub_branches')
                  ->cascadeOnDelete();
            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->cascadeOnDelete();
            
            // Índices
            $table->index(['sub_branch_id', 'product_id', 'movement_date', 'deleted_at']);
            $table->index(['movement_type', 'movement_date', 'deleted_at']);
            $table->index(['reference_type', 'reference_id', 'deleted_at']);
            $table->index(['movement_date', 'deleted_at']);
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kardex');
    }
};
