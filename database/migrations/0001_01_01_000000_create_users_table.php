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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('mobile_number')->nullable();
            $table->tinyInteger('mobile_verified')->default(0)->comment('0=> not verified, 1=> verified');
            $table->tinyInteger('is_verified')->default(1)->comment('0=> not verified, 1=> verified');
            $table->tinyInteger('is_featured')->default(0)->comment('0=> not featured, 1=> featured');
            $table->boolean('is_enabled')->default(true)->comment('0 => disabled, 1 => enabled');
            $table->longText('provider_url')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0=> inactive, 1=> active');
            $table->text('social_user_id')->nullable();
            $table->enum('login_type', ['google', 'facebook', 'normal'])->nullable();
            $table->integer('resident_type')->nullable();
            $table->text('device_token', 255)->nullable();
            $table->string('current_session_id', 255)->nullable();
            $table->datetime('last_login_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->rememberToken();
            $table->longText('qr_code_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
