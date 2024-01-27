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
        Schema::create('payments',function(Blueprint $table) {
            $table->increments('id');
            $table->string('r_payment_id');
            $table->string('method')->nullable();
            $table->string('currency')->nullable();
            $table->string('user_email')->nullable();
            $table->integer('user_id');
            $table->string('amount');
            $table->integer('document_id');
            $table->integer('type')->default(0);
            $table->longText('json_response');
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
        Schema::dropIfExists('payments');
    }
};
