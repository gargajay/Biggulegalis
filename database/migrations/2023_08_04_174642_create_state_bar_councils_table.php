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
        Schema::create('state_bar_councils', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('permission_type')->default(1)->comment("1=>Open ,2 =>Invite only, 3=> Close");
            $table->text('description')->nullable();
            $table->boolean('status')->default(true); // Set the default value to true
            $table->unsignedBigInteger('country_id'); // Foreign key column
            $table->timestamps();
            $table->softDeletes();
        
            // Define the foreign key relationship
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('state_bar_councils');
    }
};
