<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('contact')->nullable();
            $table->integer('mode')->nullable()->comment('1=>Email , 2=>Mobile');
            $table->text('otp')->nullable();
            $table->integer('purpose')->nullable()->comment('e.g. forgot = 1, signup = 2,  login = 3');
            $table->string('token')->nullable();
            $table->integer('otp_counter')->default('0');
            $table->boolean('is_otp_verified')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('otp_verifications');
    }
};
