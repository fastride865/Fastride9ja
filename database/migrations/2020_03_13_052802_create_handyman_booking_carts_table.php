<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHandymanBookingCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('handyman_booking_carts', function (Blueprint $table) {
//            $table->increments('id');
//
//            $table->integer('segment_id')->unsigned();
//            $table->foreign('segment_id')->references('id')->on('segments')->onUpdate('RESTRICT')->onDelete('CASCADE');
//
//            $table->integer('merchant_id')->unsigned();
//            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');
//
//            $table->integer('country_area_id')->unsigned();
//            $table->foreign('country_area_id')->references('id')->on('country_areas')->onUpdate('RESTRICT')->onDelete('CASCADE');
//
//            $table->integer('user_id')->unsigned();
//            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
//
//            $table->integer('driver_id')->unsigned()->nullable();
//            $table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('RESTRICT')->onDelete('CASCADE');
//
//            $table->integer('promo_code_id')->unsigned()->nullable();
//            $table->foreign('promo_code_id')->references('id')->on('promo_codes')->onUpdate('RESTRICT')->onDelete('CASCADE');
//
//            $table->integer('segment_price_card_id')->unsigned()->nullable();
//            $table->foreign('segment_price_card_id')->references('id')->on('segment_price_cards')->onUpdate('RESTRICT')->onDelete('CASCADE');
//
//            $table->integer('service_time_slot_detail_id')->unsigned()->nullable();
//            $table->foreign('service_time_slot_detail_id')->references('id')->on('service_time_slot_details')->onUpdate('RESTRICT')->onDelete('CASCADE');
//
//            $table->decimal('cart_amount', 10,2)->nullable();
//
//            $table->decimal('minimum_booking_amount',10,2)->nullable();
//
//            $table->tinyInteger('price_type')->nullable()->comment('1 for fixed and 2 hourly');
//
//            $table->date('booking_date');
//
//            $table->text('service_details');
//
//            $table->timestamps();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::dropIfExists('handyman_orders');
    }
}
