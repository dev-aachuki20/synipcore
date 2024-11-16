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
        Schema::create('visitors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid');

            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('name')->nullable()->comment('guest= Guest Name, others=Company Name');
            $table->string('phone_number')->nullable();

            $table->string('cab_number')->nullable()->comment('cab=value, other=null');

            $table->dateTime('visit_date')->nullable()->comment('null for cab');

            $table->boolean('keep_package')->default(0)->nullable();

            $table->enum('visitor_type', ['guest', 'cab', 'delivery_man', 'service_man'])->nullable();

            $table->string('gatepass_code')->nullable();
            $table->longText('qr_code')->nullable();

            $table->text('other_info')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
