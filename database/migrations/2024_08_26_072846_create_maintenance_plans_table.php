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
        Schema::create('maintenance_plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('society_id')->constrained('societies')->nullable();
            $table->integer('year_of');
            // $table->foreignId('category_id')->constrained('categories')->nullable();
            // $table->foreignId('maintenance_item_id')->constrained('maintenance_items')->nullable();
            // $table->json('month')->nullable(); // JSON field to store multiple months
            $table->decimal('total_budget', 15, 2);
            // $table->text('comments')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=> active, 0=>inactive');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_plans');
    }
};
