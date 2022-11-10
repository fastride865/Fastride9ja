<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryCheckoutDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_checkout_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('booking_checkout_id');
            $table->foreign('booking_checkout_id')->references('id')->on('booking_checkouts')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->integer('stop_no');
            $table->string('drop_latitude');
            $table->string('drop_longitude');
            $table->string('drop_location');
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->text('receiver_image')->nullable();
            $table->text('additional_notes')->nullable();
            $table->longText('product_data')->nullable();
            $table->string('product_image_one')->nullable();
            $table->string('product_image_two')->nullable();
            $table->tinyInteger('details_fill_status')->default(0)->comment('0 - Not filled, 1 - filled');
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
        Schema::dropIfExists('delivery_checkout_details');
    }
}
