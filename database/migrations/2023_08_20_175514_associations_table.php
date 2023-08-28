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
        Schema::create('associations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('permission_type')->default(1)->comment("1=>Open ,2 =>Invite only, 3=> Close");
            $table->text('description')->nullable();
            $table->boolean('status')->default(true); 
            $table->integer('parent_id')->default(0); 
            $table->integer('association_type')->default(1)->comment("bar_council_of_india=>1,state_bar_councils=>2,district_bar_councils=>3,tehsile/other=>4"); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('associations');
    }
};
