<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('orders'))
        {
        Schema::create('orders', function (Blueprint $table) {

            $table->increments('id');

            $table->integer('segment_id')->unsigned();
            $table->foreign('segment_id')->references('id')->on('segments')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('merchant_id')->unsigned();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('merchant_order_id');

            $table->integer('business_segment_id')->unsigned()->comment('product supplier');
            $table->foreign('business_segment_id')->references('id')->on('business_segments')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('country_area_id')->unsigned();
            $table->foreign('country_area_id')->references('id')->on('country_areas')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('service_type_id')->nullable()->unsigned();
            $table->foreign('service_type_id')->references('id')->on('service_types')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('price_card_id')->nullable()->unsigned();
            $table->foreign('price_card_id')->references('id')->on('price_cards')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('payment_method_id')->unsigned();
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onUpdate('RESTRICT');

            $table->integer('payment_option_id')->nullable()->unsigned();
            $table->foreign('payment_option_id')->references('id')->on('payment_options')->onUpdate('RESTRICT');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('driver_id')->unsigned()->nullable();
            $table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('driver_vehicle_id')->nullable()->unsigned();
            $table->foreign('driver_vehicle_id')->references('id')->on('driver_vehicles')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('promo_code_id')->unsigned()->nullable();
            $table->foreign('promo_code_id')->references('id')->on('promo_codes')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->tinyInteger('order_status')->default(1)->comment('1:generated 2:accepted 3:cancelled 4:picked 5:delivered');


//            $table->string('pickup_latitude', 100);
//            $table->string('pickup_longitude', 100);
//
            $table->integer('user_address_id')->unsigned()->nullable();
            $table->foreign('user_address_id')->references('id')->on('user_addresses')->onUpdate('RESTRICT');

            $table->integer('service_time_slot_id')->unsigned()->nullable();
            $table->foreign('service_time_slot_id')->references('id')->on('service_time_slots')->onUpdate('RESTRICT');

            $table->integer('service_time_slot_detail_id')->unsigned()->nullable();
            $table->foreign('service_time_slot_detail_id')->references('id')->on('service_time_slot_details')->onUpdate('RESTRICT');

            $table->string('drop_latitude', 100)->nullable();
            $table->string('drop_longitude', 100)->nullable();
//
//            $table->string('pickup_location', 191)->nullable();
            $table->string('drop_location', 191);

            $table->string('estimate_driver_distance', 10)->nullable();
            $table->string('estimate_driver_time', 20)->nullable();

            $table->string('estimate_distance', 50); // distance b/w restro and user
            $table->string('travel_distance', 10); // distance b/w restro and user
            $table->string('estimate_time', 10); // time b/w restro and user
            $table->string('travel_time', 10); // time b/w restro and user

            $table->integer('quantity')->nullable();
            $table->decimal('cart_amount', 10,2)->nullable();
            $table->decimal('estimate_amount', 10,2)->nullable();
            $table->decimal('tax', 10,2)->nullable();
            $table->decimal('discount_amount',10,2)->nullable();
            $table->decimal('time_charges',10,2)->nullable();
            $table->decimal('delivery_amount', 10,2)->nullable();
            $table->decimal('tip_amount', 10,2)->nullable();
            $table->decimal('final_amount_paid', 10,2)->nullable();

//            $table->decimal('company_cut',10,2)->nullable();
//            $table->decimal('driver_cut',10,2)->nullable();
            $table->text('bill_details')->nullable();

            $table->integer('card_id')->unsigned()->nullable();
            $table->foreign('card_id')->references('id')->on('user_cards')->onUpdate('RESTRICT');

            $table->tinyInteger('payment_status')->comment('1 Paid else pending');
//            $table->string('transaction_id');

            $table->text('order_status_history')->nullable();
            $table->text('poly_points')->nullable();
            $table->text('additional_notes')->nullable();
            $table->integer('cancel_reason_id')->nullable();
            $table->string('unique_id', 100)->nullable();
            $table->text('map_image')->nullable();
            $table->text('prescription_image')->nullable();
            $table->integer('platform')->default(1)->comment('1:app, 2:admin,3:website');
            $table->string('settlement', 11)->nullable();
            $table->string('order_timestamp',50);
            $table->date('order_date')->nullable();
            $table->string('otp_for_pickup')->nullable();
            $table->tinyInteger('confirmed_otp_for_pickup')->nullable()->default(2)->comment("1:Yes, 2:No");;
            $table->tinyInteger('is_order_completed')->default(2)->comment("1:Yes, 0:No");
            $table->tinyInteger('refund')->nullable()->comment("1:Yes, 2:No");

            $table->tinyInteger('reassign')->nullable()->comment("1:Yes");
            $table->tinyInteger('order_type')->default(1)->comment("1:Now 2: Later");
            // old driver which has accepted the order but couldn't deliver
            $table->integer('old_driver_id')->unsigned()->nullable();
            $table->foreign('old_driver_id')->references('id')->on('drivers')->onUpdate('RESTRICT');

           // reassign reason
            $table->text('reassign_reason')->nullable()->comment("reason of reassigning the order");

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
        Schema::dropIfExists('orders');
    }
}
