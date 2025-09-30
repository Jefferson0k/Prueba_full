<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('providers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ruc', 11)->unique();
            $table->string('razon_social');
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('ruc');
            $table->index(['created_by', 'updated_by']);
        });
    }

    public function down() {
        Schema::dropIfExists('providers');
    }
};
