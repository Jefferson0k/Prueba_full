<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->uuid('product_id');
            $table->integer('current_stock');
            $table->integer('min_stock')->default(0);
            $table->integer('max_stock')->nullable();
            $table->decimal('average_cost', 10, 2)->default(0);
            
            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('product_id')->references('id')->on('products');
            
            // Índices
            $table->unique(['branch_id', 'product_id', 'deleted_at'], 'unique_branch_product_inventory');
            $table->index(['branch_id', 'current_stock']);
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory');
    }
};
