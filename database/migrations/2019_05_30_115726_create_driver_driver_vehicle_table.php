<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriverDriverVehicleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('driver_driver_vehicle', function(Blueprint $table)
		{
			$table->integer('driver_id')->unsigned()->index('driver_driver_vehicle_driver_id_foreign');
			$table->integer('driver_vehicle_id')->unsigned()->index('driver_driver_vehicle_driver_vehicle_id_foreign');
            $table->tinyInteger('vehicle_active_status')->default(2)->comment('1: Active,2: Deactive ');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('driver_driver_vehicle');
	}

}
