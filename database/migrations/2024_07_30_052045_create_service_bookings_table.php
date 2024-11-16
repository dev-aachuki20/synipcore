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
        Schema::create('service_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid');

            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->text('description')->nullable();
            $table->date('booking_date')->nullable();
            $table->time('booking_time')->nullable();

            $table->enum('status', ['pending', 'accept', 'reject'])->default('pending')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_bookings');
    }
};
