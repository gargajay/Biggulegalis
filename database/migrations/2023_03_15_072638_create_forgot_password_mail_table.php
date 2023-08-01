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
        Schema::create('forgot_password_mail', function (Blueprint $table) {
            $table->string('token', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->integer('id', true);
            $table->string('user_type')->default('user')->comment('user,admin,superadmin');
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
        Schema::dropIfExists('forgot_password_mail');
    }
};
