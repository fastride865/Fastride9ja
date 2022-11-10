<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingDeliveryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_delivery_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('booking_id');
            $table->foreign('booking_id')->references('id')->on('bookings')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->integer('stop_no');
            $table->string('drop_latitude');
            $table->string('drop_longitude');
            $table->string('drop_location');
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->text('receiver_image')->nullable();
            $table->longText('product_data')->nullable();
            $table->string('product_image_one')->nullable();
            $table->string('product_image_two')->nullable();
            $table->text('additional_notes')->nullable();
            $table->string('opt_for_verify');
            $table->tinyInteger('otp_status')->default(0)->comment('0 - Not Verify, 1 - Verifty');
            $table->tinyInteger('drop_status')->default(0)->comment('0 - Not Drop, 1 - Drop');
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
        Schema::dropIfExists('booking_delivery_details');
    }
}
