<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('room_status_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('room_id');
            $table->uuid('booking_id')->nullable();
            $table->string('previous_status');
            $table->string('new_status');
            $table->text('reason')->nullable();
            $table->datetime('changed_at');
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamps();

            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');

            // Índices
            $table->index(['room_id', 'changed_at']);
            $table->index(['new_status', 'changed_at']);
            $table->index('changed_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_status_logs');
    }
};