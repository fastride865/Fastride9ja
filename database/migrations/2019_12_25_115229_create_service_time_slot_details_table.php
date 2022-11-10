<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceTimeSlotDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_time_slot_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_time_slot_id')->unsigned();
            $table->foreign('service_time_slot_id')->references('id')->on('service_time_slots')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->string('title',50)->nullable();
            $table->time('from_time');
            $table->time('to_time');
            $table->string('slot_time_text',20)->comment('just to display');
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
        Schema::dropIfExists('service_time_slot_details');
    }
}
