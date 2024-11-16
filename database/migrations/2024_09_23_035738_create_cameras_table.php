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
        Schema::create('cameras', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('camera_id')->nullable();
            $table->text('lacated_at')->nullable();
            $table->foreignId('society_id')->constrained('societies')->nullable();
            $table->foreignId('building_id')->nullable()->constrained('buildings')->nullable();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(1)->comment('1=> active, 0=>inactive');
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
        Schema::dropIfExists('cameras');
    }
};
