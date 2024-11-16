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
        Schema::create('resident_frequest_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();
            
            $table->unsignedBigInteger('resident_id')->nullable();

            $table->string('name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('task')->nullable();

            $table->string('gatepass_code')->nullable();

            $table->tinyInteger('status')->default(1)->comment('1=> active, 0=>inactive');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resident_frequest_entries');
    }
};
