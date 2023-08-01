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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('payment_to')->unsigned()->nullable();
            $table->foreign('payment_to')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('card_id')->unsigned()->nullable();
            // $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
            $table->string('charge_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->double('amount', 10, 2)->default(0);
            $table->string('currency')->nullable()->default('usd');
            $table->string('payment_message')->nullable();
            $table->string('payment_status')->nullable();
            $table->softDeletes();
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
