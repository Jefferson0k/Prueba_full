a<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('category_id');
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            
            // Precios
            $table->decimal('purchase_price', 10, 2); // Precio de compra
            $table->decimal('sale_price', 10, 2);     // Precio de venta
            
            $table->enum('unit_type', ['piece', 'bottle', 'pack', 'kg', 'liter']);
            $table->boolean('is_active')->default(true);
            
            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('category_id')->references('id')->on('product_categories');
            
            // Índices
            $table->index(['category_id', 'is_active', 'deleted_at']);
            $table->index(['code', 'deleted_at']);
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
