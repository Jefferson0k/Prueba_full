<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sub_branch_id'); // cada local tiene su caja
            $table->string('name')->default('Caja principal');
            $table->enum('status', ['abierta', 'cerrada', 'bloqueada'])->default('cerrada');
            $table->integer('opened_by')->nullable();  // usuario que abrió la caja
            $table->integer('closed_by')->nullable();  // usuario que la cerró
            $table->decimal('opening_amount', 12, 2)->default(0);
            $table->decimal('closing_amount', 12, 2)->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->boolean('is_active')->default(true);

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Relaciones
            $table->foreign('sub_branch_id')
                ->references('id')->on('sub_branches')
                ->cascadeOnDelete();

            $table->index(['sub_branch_id', 'status', 'deleted_at']);
            $table->index(['opened_by', 'closed_by']);
        });
    }

    public function down() {
        Schema::dropIfExists('cash_registers');
    }
};
