<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFailBookingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('fail_bookings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id');
			$table->integer('user_id');
			$table->integer('country_area_id');
			$table->integer('service_type_id');
			$table->integer('vehicle_type_id');
			$table->integer('service_package_id')->nullable();
			$table->string('pickup_latitude', 191);
			$table->string('pickup_longitude', 191);
			$table->string('pickup_location', 191);
			$table->string('drop_location', 191)->nullable();
			$table->integer('failreason');
			$table->string('booking_type', 191);
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
		Schema::drop('fail_bookings');
	}

}
