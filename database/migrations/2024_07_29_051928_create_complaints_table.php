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
        Schema::create('complaints', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('complaint_type_id')->nullable();

            $table->enum('category', ['personal', 'community'])->nullable();
            $table->longText('description')->nullable();
            
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'rejected'])->default('pending');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
