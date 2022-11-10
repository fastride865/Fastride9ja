<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPoolRideListsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pool_ride_lists', function(Blueprint $table)
		{
			$table->foreign('booking_id')->references('id')->on('bookings')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('pool_ride_lists', function(Blueprint $table)
		{
			$table->dropForeign('pool_ride_lists_booking_id_foreign');
			$table->dropForeign('pool_ride_lists_driver_id_foreign');
			$table->dropForeign('pool_ride_lists_user_id_foreign');
		});
	}

}
