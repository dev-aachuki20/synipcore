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
        Schema::create('guard_messages', function (Blueprint $table) {
            $table->id();
            $table->integer('resident_id')->nullable();
            $table->integer('guard_id')->nullable();
            $table->longText('message')->nullable();
            $table->boolean('status')->default(1)->comment('0 => Inactive, 1 => Active');

            $table->dateTime('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guard_messages');
    }
};
