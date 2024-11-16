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
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid');

            $table->string('title', 255)->nullable();
            $table->decimal('amount', 10,2)->nullable();
            $table->date('due_date')->nullable();

            $table->unsignedBigInteger('society_id')->nullable();
            $table->unsignedBigInteger('building_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();

            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending')->nullable();

            $table->dateTime('paid_at')->nullable();

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
        Schema::dropIfExists('payment_requests');
    }
};
