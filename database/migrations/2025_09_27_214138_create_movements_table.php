<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->date('date');

            // Vinculación
            $table->uuid('provider_id');
            $table->uuid('sub_branch_id');

            // Datos del movimiento
            $table->enum('payment_type', ['credito', 'contado']);
            $table->date('credit_date')->nullable(); // Fecha de crédito, solo para tipo 'credito'
            $table->boolean('includes_igv')->default(true);
            $table->enum('voucher_type', ['factura', 'boleta', 'otros'])->default('otros');

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('provider_id')->references('id')->on('providers');
            $table->foreign('sub_branch_id')->references('id')->on('sub_branches');

            // Índices
            $table->index(['provider_id', 'sub_branch_id']);
        });
    }

    public function down() {
        Schema::dropIfExists('movements');
    }
};
