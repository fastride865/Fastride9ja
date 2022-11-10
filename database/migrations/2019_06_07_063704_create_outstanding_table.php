<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutstandingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('outstanding')) {
            Schema::create('outstanding', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
                $table->unsignedInteger('booking_id')->nullable();
                $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('CASCADE');
                $table->unsignedInteger('driver_id');
                $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('CASCADE');
                $table->double('amount');
                $table->unsignedInteger('handyman_order_id')->nullable();
                $table->tinyInteger('reason')->default(1)->comment('1 - Cancel Outstanding, 2 - Ride Amount Outstanding,3:Handyman Booking Outstanding');
                $table->tinyInteger('pay_status')->default(0)->comment('0 - Unpaid, 1 - Paid');
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
        Schema::dropIfExists('outstanding');
    }
}
