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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('subscription_type')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('purchase_token')->nullable();
            $table->string('expiry_date')->nullable();
            $table->double('amount', 10, 2)->default(0);
            $table->string('duration')->nullable();
            $table->longText('description')->nullable();
            $table->string('payment_with')->nullable();
            $table->string('product_id')->nullable();
            $table->string('original_transaction_product_id')->nullable();
            $table->text('server_request_data')->nullable();
            $table->boolean('status')->default(true);
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
        DB::statement('DROP TABLE IF EXISTS subscriptions CASCADE');
    }
};
