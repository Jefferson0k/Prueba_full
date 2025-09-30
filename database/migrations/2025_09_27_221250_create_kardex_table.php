<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('kardex', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Referencias
            $table->uuid('product_id');
            $table->uuid('sub_branch_id');           // agregado
            $table->uuid('movement_id')->nullable();
            $table->uuid('sale_id')->nullable();

            // Datos de inventario
            $table->double('precio_total', 8, 2)->default(0.00);
            $table->double('SAnteriorCaja', 8, 2)->default(0.00);
            $table->double('SAnteriorFraccion', 8, 2)->default(0.00);
            $table->double('cantidadCaja', 8, 2)->default(0.00);
            $table->double('cantidadFraccion', 8, 2)->default(0.00);
            $table->double('SParcialCaja', 8, 2)->default(0.00);
            $table->double('SParcialFraccion', 8, 2)->default(0.00);

            // Tipo de movimiento
            $table->enum('movement_type', ['entrada', 'salida'])->default('entrada');
            $table->enum('movement_category', ['compra', 'venta', 'ajuste', 'otros'])->default('otros');

            $table->tinyInteger('estado')->default(1); // activo/inactivo

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('sub_branch_id')->references('id')->on('sub_branches');
            $table->foreign('movement_id')->references('id')->on('movements');
            $table->foreign('sale_id')->references('id')->on('sales');

            // Índices
            $table->index(['product_id', 'sub_branch_id', 'movement_id', 'sale_id']);
        });
    }

    public function down() {
        Schema::dropIfExists('kardex');
    }
};
