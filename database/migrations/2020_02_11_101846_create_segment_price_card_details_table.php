<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegmentPriceCardDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('segment_price_card_details', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('segment_price_card_id')->unsigned()->nullable();
            $table->foreign('segment_price_card_id')->references('id')->on('segment_price_cards')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->integer('service_type_id')->unsigned()->nullable();
            $table->foreign('service_type_id')->references('id')->on('service_types')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->float('amount',8,2)->nullable();
            $table->tinyInteger('delete')->nullable()->comment('1:Yes,2:No');
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
        Schema::dropIfExists('segment_price_cards');
    }
}
