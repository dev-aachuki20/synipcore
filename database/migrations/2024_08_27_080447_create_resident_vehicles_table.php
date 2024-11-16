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
        Schema::create('resident_vehicles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('resident_id')->nullable()->constrained('users');
            $table->string('vehicle_number')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->string('vehicle_color')->nullable();
            $table->string('parking_slot_no')->nullable();

            $table->foreignId('society_id')->nullable()->constrained('societies');
            $table->foreignId('building_id')->nullable()->constrained('buildings');
            $table->foreignId('unit_id')->nullable()->constrained('units');

            $table->string('gatepass_code')->nullable();
            $table->longText('qr_code')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected', 'in', 'out'])->default('pending')->nullable();
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
        Schema::dropIfExists('resident_vehicles');
    }
};
