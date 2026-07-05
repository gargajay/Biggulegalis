<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('chat_requests')) {
            Schema::create('chat_requests', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('requester_id');
                $table->unsignedBigInteger('receiver_id');
                $table->unsignedBigInteger('chat_id')->nullable();
                $table->tinyInteger('status')->default(0); // 0 pending, 1 accepted, 2 denied
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('chat_id');
                $table->unsignedBigInteger('sender_id');
                $table->unsignedBigInteger('receiver_id');
                $table->text('message')->nullable();
                $table->string('media_type')->nullable();
                $table->string('media_url')->nullable();
                $table->string('media_extension')->nullable();
                $table->boolean('is_seen')->default(false);
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('chat_requests');
        Schema::dropIfExists('messages');
    }
};
