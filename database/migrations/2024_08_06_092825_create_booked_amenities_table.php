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
        Schema::create('amenity_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('amenity_id')->nullable();

            $table->date('from_date')->nullable();
            $table->time('from_time')->nullable();

            $table->date('to_date')->nullable();
            $table->time('to_time')->nullable();

            $table->decimal('amount', 10, 2)->nullable();
            $table->integer('max_users_capacity')->nullable();


            $table->enum('payment_status', ['pending', 'completed', 'failed', 'cancelled', 'refund'])->default('pending')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->nullable();
            $table->boolean('is_full_day')->default(1)->nullable()->comment('0 => Not full day, 1 => full day');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booked_amenities');
    }
};
