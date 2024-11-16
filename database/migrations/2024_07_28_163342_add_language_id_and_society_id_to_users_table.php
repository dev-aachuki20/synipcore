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
        Schema::table('users', function (Blueprint $table) {

            $table->unsignedBigInteger('language_id')->nullable()->after('is_verified');
            $table->foreign('language_id')->references('id')->on('languages')->nullable();

            $table->unsignedBigInteger('society_id')->nullable()->after('language_id');
            $table->foreign('society_id')->references('id')->on('societies');

            $table->unsignedBigInteger('building_id')->nullable()->after('society_id');
            $table->foreign('building_id')->references('id')->on('buildings');

            $table->unsignedBigInteger('unit_id')->nullable()->after('building_id');
            $table->foreign('unit_id')->references('id')->on('units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['language_id']);
            $table->dropColumn('language_id');
            
            $table->dropForeign(['society_id']);
            $table->dropColumn('society_id');

            $table->dropForeign(['building_id']);
            $table->dropColumn('building_id');

            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
        });
    }
};
