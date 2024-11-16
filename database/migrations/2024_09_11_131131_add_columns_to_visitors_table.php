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
        Schema::table('visitors', function (Blueprint $table) {
            $table->longText('visitor_note')->nullable()->after('visitor_type');
            $table->enum('status', ['pending', 'approved', 'rejected', 'in', 'out'])->default('approved')->after('gatepass_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropColumn('status', 'visitor_note');
        });
    }
};
