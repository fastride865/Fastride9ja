<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('user_addresses'))
        {
            Schema::create('user_addresses', function (Blueprint $table) {
                $table->increments('id');

                $table->integer('user_id')->unsigned();
                $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');

                $table->string('house_name')->nullable();
                $table->string('floor')->nullable();
                $table->string('building')->nullable();
                $table->string('land_mark')->nullable();
                $table->string('address');
                $table->string('latitude');
                $table->string('longitude');
                $table->tinyInteger('category')->nullable()->comment('1 : Home, 2: Work, 3: Other, Null for food and handyman based address');
                $table->string('address_title')->nullable()->comment('it will fill when category is 3');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_addresses');
    }
}
