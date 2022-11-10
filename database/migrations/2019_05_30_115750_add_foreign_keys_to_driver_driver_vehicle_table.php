<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDriverDriverVehicleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('driver_driver_vehicle', function(Blueprint $table)
		{
			$table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('driver_vehicle_id')->references('id')->on('driver_vehicles')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('driver_driver_vehicle', function(Blueprint $table)
		{
			$table->dropForeign('driver_driver_vehicle_driver_id_foreign');
			$table->dropForeign('driver_driver_vehicle_driver_vehicle_id_foreign');
		});
	}

}
