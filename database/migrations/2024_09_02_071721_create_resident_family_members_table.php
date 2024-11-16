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
        Schema::create('resident_family_members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();

            $table->unsignedBigInteger('resident_id')->nullable();

            $table->string('name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('relation')->nullable();

            $table->string('gatepass_code')->nullable();
            $table->longText('qr_code')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'in', 'out'])->default('pending')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resident_family_members');
    }
};
