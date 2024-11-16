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
        Schema::create('property_management', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('property_type_id')->constrained('property_types')->nullable();
            $table->foreignId('society_id')->constrained('societies')->nullable();
            $table->unsignedBigInteger('building_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->text('property_item')->nullable();
            $table->text('property_code')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->longText('location')->nullable();
            $table->longText('allocation')->nullable();
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
        Schema::dropIfExists('property_management');
    }
};
