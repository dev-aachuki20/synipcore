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
        Schema::create('security_alerts', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('resident_id')->nullable();

            $table->enum('alert_type', ['fire_alert', 'lift_alert', 'animal_alert', 'visitor_alert', 'other_alert'])->nullable();
            
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
        Schema::dropIfExists('security_alerts');
    }
};
