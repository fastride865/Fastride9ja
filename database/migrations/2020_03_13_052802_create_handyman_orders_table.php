<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHandymanOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('handyman_orders', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('segment_id')->unsigned();
            $table->foreign('segment_id')->references('id')->on('segments')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('merchant_id')->unsigned();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('merchant_order_id');

            $table->integer('country_area_id')->unsigned();
            $table->foreign('country_area_id')->references('id')->on('country_areas')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('payment_method_id')->unsigned();
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('driver_id')->unsigned()->nullable();
            $table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('promo_code_id')->unsigned()->nullable();
            $table->foreign('promo_code_id')->references('id')->on('promo_codes')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('service_time_slot_detail_id')->unsigned()->nullable();
            $table->foreign('service_time_slot_detail_id')->references('id')->on('service_time_slot_details')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('segment_price_card_id')->unsigned()->nullable();
            $table->foreign('segment_price_card_id')->references('id')->on('segment_price_cards')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('order_status')->default(1)->comment('1:generated 2:accepted 3:cancelled 4:picked 5:delivered');

            $table->integer('min_booking_payment_method_id')->nullable()->unsigned();
            $table->foreign('min_booking_payment_method_id')->references('id')->on('payment_methods')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->tinyInteger('minimum_booking_amount_payment_status')->nullable();
            $table->tinyInteger('advance_payment_of_min_bill')->nullable();

            $table->tinyInteger('price_type')->nullable()->comment('1 for fixed and 2 hourly');
            $table->integer('quantity')->nullable();
            $table->decimal('cart_amount', 10,2)->nullable();
            $table->decimal('discount_amount',10,2)->nullable();
            $table->decimal('tip_amount',10,2)->nullable();
            $table->tinyInteger('tax_per')->nullable();
            $table->decimal('tax', 10,2)->nullable();

            $table->decimal('extra_charges', 10,2)->nullable();
            $table->text('extra_charges_details');

            $table->tinyInteger('total_service_hours')->nullable();
            $table->decimal('hourly_amount',10,1)->nullable();

            $table->decimal('minimum_booking_amount', 10,2)->nullable()->comment('including tax');
            $table->decimal('total_booking_amount', 10,2)->nullable()->comment("total booking amount after discount and tax");
            $table->decimal('final_amount_paid', 10,2)->nullable()->comment("final paid amount by user it equal either min booking amount or total booking amount");

            $table->integer('card_id')->unsigned()->nullable();
            $table->foreign('card_id')->references('id')->on('user_cards')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->text('order_status_history')->nullable(); // maintain the status history
            $table->date('booking_date')->nullable();
            $table->integer('order_otp')->nullable()->comment('order start otp');
            $table->string('booking_timestamp')->nullable();
            $table->string('drop_latitude')->nullable();
            $table->string('drop_longitude')->nullable();
            $table->string('drop_location')->nullable();
            $table->text('additional_notes')->nullable();
            $table->integer('cancel_reason_id')->nullable();
            $table->integer('payment_status')->default(2)->comment('1:Yes, 2: NO');
            $table->integer('is_order_completed')->default(2)->comment('1:Yes, 2: NO');
            $table->text('map_image')->nullable();
//            $table->text('booking_images')->nullable(); merged it with drive gallery

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
        Schema::dropIfExists('handyman_orders');
    }
}
