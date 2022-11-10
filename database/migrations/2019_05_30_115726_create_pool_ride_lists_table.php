<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoolRideListsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pool_ride_lists', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('booking_id')->unsigned()->index('pool_ride_lists_booking_id_foreign');
			$table->integer('driver_id')->unsigned()->nullable()->index('pool_ride_lists_driver_id_foreign');
			$table->integer('user_id')->unsigned()->index('pool_ride_lists_user_id_foreign');
			$table->integer('riders_number');
			$table->integer('pickup')->default(0);
			$table->integer('dropped')->default(0);
			$table->string('pickup_lat', 191);
			$table->string('pickup_long', 191);
			$table->string('drop_lat', 191);
			$table->string('drop_long', 191);
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pool_ride_lists');
	}

}
