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
        Schema::create('poll_votes', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('notice_board_id')->nullable()->index();
            $table->unsignedBigInteger('poll_option_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();

            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poll_votes');
    }
};
