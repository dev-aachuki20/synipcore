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
        Schema::create('ai_box_alerts', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('society_id')->nullable()->index();
            $table->unsignedBigInteger('building_id')->nullable()->index();
            $table->unsignedBigInteger('unit_id')->nullable()->index();

            $table->unsignedBigInteger('camera_id')->nullable()->index();

            $table->longText('notification_data')->nullable();

            $table->string('api_type')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_box_alerts');
    }
};
