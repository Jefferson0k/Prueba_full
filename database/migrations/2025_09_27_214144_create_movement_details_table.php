<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('movement_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('movement_id');
            $table->uuid('product_id');
            $table->decimal('unit_price', 10, 2);

            // Cantidades
            $table->integer('boxes')->default(0);
            $table->integer('units_per_box')->default(1);
            $table->integer('extra_units')->default(0); // unidades sueltas
            $table->date('expiry_date')->nullable();

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('movement_id')->references('id')->on('movements')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products');

            // Índices
            $table->index(['movement_id', 'product_id']);
        });
    }

    public function down() {
        Schema::dropIfExists('movement_details');
    }
};
