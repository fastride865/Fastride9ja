<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_packages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('booking_id');
            $table->foreign('booking_id')->references('id')->on('bookings')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('booking_delivery_detail_id');
            $table->foreign('booking_delivery_detail_id')->references('id')->on('booking_delivery_details')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('delivery_product_id');
            $table->foreign('delivery_product_id')->references('id')->on('delivery_products')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->string('quantity');
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
        Schema::dropIfExists('delivery_packages');
    }
}
