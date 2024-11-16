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
        Schema::create('maintenance_plan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maintenance_plan_id')->nullable();
            $table->unsignedBigInteger('maintenance_item_id')->nullable();
            $table->json('month');
            $table->string('budget', '10')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_plan_items');
    }
};
