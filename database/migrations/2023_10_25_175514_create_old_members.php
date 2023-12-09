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
        Schema::create('old_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('association_id'); // Foreign key column
            $table->string('phone_no')->nullable();
            $table->string('full_name')->nullable();
            $table->string('enrolment_number')->nullable();
            $table->string('image')->nullable();
            $table->string('year')->nullable();
            $table->json('roles')->nullable(); // New column
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->softDeletes();
            // Define the foreign key relationship
            $table->foreign('association_id')->references('id')->on('associations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('old_members');
    }
};
