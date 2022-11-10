<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookingRatingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('booking_ratings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('booking_id')->unsigned()->nullable();
			$table->integer('order_id')->unsigned()->nullable();
			$table->integer('handyman_order_id')->unsigned()->nullable();
			$table->string('user_rating_points', 191)->nullable();
			$table->text('user_comment')->nullable(); // user's comment for driver
			$table->string('driver_rating_points', 191)->nullable();
			$table->text('driver_comment')->nullable(); // driver's comment for user
			$table->string('driver_vehicle_rating_points', 191)->nullable();
			$table->string('driver_vehicle_comment', 191)->nullable();
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
		Schema::drop('booking_ratings');
	}

}
