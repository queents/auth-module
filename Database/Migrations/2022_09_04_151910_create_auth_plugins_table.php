<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_plugins', function (Blueprint $table) {
            $table->id();
            $table->integer('model_id')->nullable();
            $table->text('model_type')->nullable();
            $table->text('otp')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('password');
            $table->boolean('activated')->default(0);
            $table->boolean('blocked')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auth_plugins');
    }
};
