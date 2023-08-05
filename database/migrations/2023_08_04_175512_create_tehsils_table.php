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
        Schema::create('tehsils', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('status')->default(true); // Set the default value to true
            $table->unsignedBigInteger('district_bar_association_id'); // Foreign key column
            $table->timestamps();
            $table->softDeletes();
        
            // Define the foreign key relationship
            $table->foreign('district_bar_association_id')->references('id')->on('district_bar_associations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tehsils');
    }
};
