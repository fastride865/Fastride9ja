<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceTimeSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_time_slots', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('merchant_id')->unsigned();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('country_area_id')->unsigned();
            $table->foreign('country_area_id')->references('id')->on('country_areas')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('segment_id')->unsigned();
            $table->foreign('segment_id')->references('id')->on('segments')->onUpdate('RESTRICT')->onDelete('CASCADE');

//            $table->integer('service_type_id')->unsigned();
//            $table->foreign('service_type_id')->references('id')->on('service_types')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->tinyInteger('day');
            $table->string('start_time');
            $table->string('end_time');
            $table->tinyInteger('max_slot')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1 Active, 2: Inactive');
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
        Schema::dropIfExists('service_time_slots');
    }
}
