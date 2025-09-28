<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 3)->unique();
            $table->string('name');
            $table->string('symbol');
            $table->decimal('exchange_rate', 12, 6)->default(1.000000);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            
            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['is_active', 'deleted_at']);
            $table->index('is_default');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('currencies');
    }
};