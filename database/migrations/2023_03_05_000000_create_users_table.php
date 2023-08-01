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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('address_id')->unsigned()->nullable();
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('set null');
            $table->string('full_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('country_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('user_type')->default('user'); // User,Admin,SuperAdmin
            $table->string('facebook_id')->nullable()->comment('for type 1');
            $table->string('google_id')->nullable()->comment('for type 2');
            $table->string('apple_id')->nullable()->comment('for type 3');
            $table->string('twitter_id')->nullable()->comment('for type 4');
            $table->string('instagram_id')->nullable()->comment('for type 5');
            $table->enum('account_type', ['1', '2', '3'])->default('1')->comment('1=>Normal , 2=>Social , 3=>Both');
            $table->enum('device_type', ['I', 'A'])->nullable()->comment('I=IOS,A=Android');
            $table->string('device_token')->nullable();
            $table->enum('push_notification', ['1', '2'])->default('1')->comment('1=>On , 2=>Off');
            $table->enum('email_push_notification', ['1', '2'])->default('1')->comment('1=>On , 2=>Off');
            $table->enum('phone_push_notification', ['1', '2'])->default('1')->comment('1=>On , 2=>Off');
            $table->string('image')->nullable();
            $table->string('social_image_url')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('timezone')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('biography')->nullable();
            $table->string('gender')->nullable();
            $table->string('language')->default('en');
            $table->string('stripe_id')->nullable();
            $table->boolean('blocked')->default(false);
            $table->enum('is_profile_completed', ['1', '2'])->default('1')->comment('1=>No , 2=>Yes');
            $table->string('city')->nullable();
            $table->string('annual_income')->nullable();
            $table->string('occupation')->nullable();
            $table->string('company')->nullable();
            $table->string('height')->nullable();
            $table->string('body_shape')->nullable();
            $table->string('ethnicity')->nullable();
            $table->string('hair_color')->nullable();
            $table->string('eye_color')->nullable();
            $table->string('relationship_status')->nullable();
            $table->string('children')->nullable();
            $table->string('smoking')->nullable();
            $table->string('drinking')->nullable();
            $table->string('diet')->nullable();
            $table->string('character')->nullable();
            $table->string('fashion_type')->nullable();
            $table->string('hobby')->nullable();
            $table->boolean('complete_status')->default(false);
            $table->string('blood_type')->nullable();
            $table->rememberToken();
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
        DB::statement('DROP TABLE IF EXISTS users CASCADE');
    }
};
