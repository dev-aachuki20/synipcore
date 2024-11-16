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
        Schema::create('societies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name')->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('city')->nullable();
            $table->unsignedBigInteger('district')->nullable();
            $table->tinyInteger('fire_alert')->default(0);
            $table->tinyInteger('lift_alert')->default(0);
            $table->tinyInteger('animal_alert')->default(0);
            $table->tinyInteger('visitor_alert')->default(0);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=> active, 0=>inactive');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('societies');
    }
};
