<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('subscription_id')->unsigned()->nullable();
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('set null');
            $table->bigInteger('stripe_card_id')->unsigned()->nullable();
            $table->foreign('stripe_card_id')->references('id')->on('stripe_cards')->onDelete('set null');
            $table->integer('total_users')->default(0);
            $table->json('user_id_json')->default('[]');
            $table->string('per_user_price')->default('0.00');
            $table->string('price')->default('0.00');
            $table->dateTime('payment_date')->nullable();
            $table->string('payment_status')->default('1')->comment('1 => Pending , 2 => Succeeded, 3 => Failed');
            $table->string('currency')->nullable();
            $table->json('payment_response_json')->default('[]');
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
        DB::statement('DROP TABLE IF EXISTS subscription_payments CASCADE');
    }
};
