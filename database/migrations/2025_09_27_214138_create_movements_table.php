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
            $table->uuid('sub_branch_id'); // <-- agregado

            $table->enum('payment_type', ['credito', 'contado']);
            $table->boolean('includes_igv')->default(true);

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('provider_id')->references('id')->on('providers');
            $table->foreign('sub_branch_id')->references('id')->on('sub_branches');
        });
    }

    public function down() {
        Schema::dropIfExists('movements');
    }
};
