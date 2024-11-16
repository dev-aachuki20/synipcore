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
        Schema::create('amenities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid');

            $table->string('title', 255)->nullable();
            $table->longText('description')->nullable();
            $table->enum('fee_type', ['free', 'paid'])->nullable();
            $table->decimal('fee', 10,2)->nullable();
            $table->integer('capacity')->nullable();
            $table->integer('booking_capacity')->nullable();
            $table->integer('advance_booking_days')->nullable();

            $table->unsignedBigInteger('society_id')->nullable();
            $table->integer('max_days_per_unit')->nullable();

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amenities');
    }
};
