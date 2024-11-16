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
        Schema::create('delivery_management', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            // $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('subject')->nullable();
            $table->longText('message')->nullable();
            $table->longText('notes')->nullable();

            $table->foreignId('delivery_type_id')->constrained('delivery_types')->nullable();
            $table->foreignId('society_id')->constrained('societies')->nullable();
            $table->foreignId('building_id')->nullable()->constrained('buildings')->nullable();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullable();

            $table->integer('respondant_id')->nullable(); // Act as processed and resolved

            $table->enum('status', ['new', 'processing', 'delivered'])->default('new');
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
        Schema::dropIfExists('delivery_management');
    }
};
