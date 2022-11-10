<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriverVehicleServiceTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('driver_vehicle_service_type', function(Blueprint $table)
		{
			$table->integer('driver_vehicle_id')->unsigned()->index('driver_vehicle_service_type_driver_vehicle_id_foreign');
            $table->integer('segment_id')->unsigned();
			$table->integer('service_type_id')->unsigned()->index('driver_vehicle_service_type_service_type_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('driver_vehicle_service_type');
	}

}
