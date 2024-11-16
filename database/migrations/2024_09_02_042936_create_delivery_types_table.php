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
        Schema::create('delivery_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title')->nullable();
            $table->string('other')->nullable();
            $table->longText('description')->nullable();
            $table->enum('notify_user', ['admin', 'guard', 'resident'])->nullable();
            $table->boolean('due_payment')->default(false)->comment('0 => due, 1 => paid');
            $table->tinyInteger('status')->default(1)->comment('1=> active, 0=>inactive');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_types');
    }
};
