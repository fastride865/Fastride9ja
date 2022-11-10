<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookingRequestDriversTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('booking_request_drivers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('booking_id')->nullable();
			$table->integer('order_id')->nullable();
			$table->integer('handyman_order_id')->nullable();
			$table->integer('driver_id');
			$table->string('distance_from_pickup', 191);
			$table->integer('request_status')->default(1)->comment('1 Sending request in process','2 Accepted, 3 : Rejected, 4 Cancelled ');
			$table->integer('inside_function')->nullable();
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
		Schema::drop('booking_request_drivers');
	}

}
