<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('type')->default('string');
            $table->string('group')->default('general');
            $table->string('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_active')->default(true);
            
            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();


            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['group', 'is_active', 'deleted_at']);
            $table->index(['is_public', 'deleted_at']);
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_settings');
    }
};