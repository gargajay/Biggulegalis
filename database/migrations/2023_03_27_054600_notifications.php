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
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('send_by')->unsigned()->nullable();
            $table->foreign('send_by')->references('id')->on('users')->onDelete('set null');
            $table->bigInteger('received_by')->unsigned()->nullable();
            $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
            $table->json('title')->default('[]');
            $table->json('body')->default('[]');
            $table->json('data')->default('[]');
            $table->boolean('read')->default(false);
            $table->string('type')->nullable();
            $table->string('model_name')->nullable();
            $table->bigInteger('model_id')->nullable();

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
        DB::statement('DROP TABLE IF EXISTS notifications CASCADE');
    }
};
