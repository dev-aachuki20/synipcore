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
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();

            $table->decimal('amount', 10, 2)->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('stripe_payment_id')->nullable();

            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled', 'refund'])->default('pending')->nullable();
            
            $table->longText('payment_data')->nullable();
            $table->longText('user_data')->nullable();
            $table->longText('model_data')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
