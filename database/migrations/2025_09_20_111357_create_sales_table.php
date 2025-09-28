<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sub_branch_id');
            $table->uuid('customer_id');
            
            $table->string('sale_number')->unique(); // Número de venta correlativo
            $table->date('sale_date');
            
            // Totales
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0); // IGV u otros impuestos
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            
            $table->enum('payment_method', ['cash', 'card', 'transfer', 'mixed'])->default('cash');
            $table->enum('status', ['completed', 'cancelled', 'pending'])->default('completed');
            
            $table->text('notes')->nullable();
            
            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('sub_branch_id')
                  ->references('id')->on('sub_branches')
                  ->cascadeOnDelete();
            $table->foreign('customer_id')
                  ->references('id')->on('customers')
                  ->cascadeOnDelete();
            
            // Índices
            $table->index(['sub_branch_id', 'sale_date', 'deleted_at']);
            $table->index(['customer_id', 'deleted_at']);
            $table->index(['sale_date', 'status', 'deleted_at']);
            $table->index(['sale_number', 'deleted_at']);
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
