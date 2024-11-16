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
        Schema::create('resident_daily_helps', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('resident_id')->nullable();
            
            $table->string('name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('help_type')->nullable();
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
        Schema::dropIfExists('resident_daily_helps');
    }
};
