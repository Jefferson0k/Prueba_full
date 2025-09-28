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
        Schema::create('sub_branch_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sub_branch_id');
            $table->uuid('product_id');
            
            // Stock actual en esta sub-sucursal
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock')->default(0);
            $table->integer('max_stock')->default(0);
            
            // Precios específicos por sub-sucursal (opcional)
            $table->decimal('custom_sale_price', 10, 2)->nullable();
            
            $table->boolean('is_active')->default(true);
            
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
            $table->unique(['sub_branch_id', 'product_id', 'deleted_at'], 'unique_sub_branch_product');
            $table->index(['sub_branch_id', 'is_active', 'deleted_at']);
            $table->index(['product_id', 'is_active', 'deleted_at']);
            $table->index('current_stock');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_branch_products');
    }
};
