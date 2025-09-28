<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('branch_room_type_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->uuid('room_type_id');
            $table->uuid('rate_type_id');
            $table->decimal('price', 10, 2);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('room_type_id')->references('id')->on('room_types');
            $table->foreign('rate_type_id')->references('id')->on('rate_types');
            
            // Índices
            $table->index(['branch_id', 'room_type_id', 'rate_type_id', 'deleted_at'], 'idx_branch_room_rate_prices');
            $table->index(['effective_from', 'effective_to', 'is_active']);
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('branch_room_type_prices');
    }
};
