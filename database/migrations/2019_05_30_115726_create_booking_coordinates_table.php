<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookingCoordinatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('booking_coordinates', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('booking_id');
			$table->longText('coordinates')->nullable();
			$table->longText('booking_polyline')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('booking_coordinates');
	}

}
