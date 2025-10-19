<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Agregar nuevos campos
            $table->uuid('payment_method_id')->nullable()->after('payment_method');
            $table->uuid('cash_register_id')->nullable()->after('booking_id');
            $table->string('operation_number')->nullable()->after('reference');
            
            // Foreign keys
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
            $table->foreign('cash_register_id')->references('id')->on('cash_registers');
            
            // Nuevos índices
            $table->index(['cash_register_id', 'payment_date']);
            $table->index(['payment_method_id', 'status']);
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropForeign(['cash_register_id']);
            $table->dropColumn(['payment_method_id', 'cash_register_id', 'operation_number']);
        });
    }
};