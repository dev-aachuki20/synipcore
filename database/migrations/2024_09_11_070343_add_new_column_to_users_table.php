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
            $table->integer('security_pin')->nullable()->after('email');
            $table->tinyInteger('guard_duty_status')->default(0)->comment('1 for on duty, 0 for off duty')->after('unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('security_pin');
            $table->dropColumn('guard_duty_status');
        });
    }
};
