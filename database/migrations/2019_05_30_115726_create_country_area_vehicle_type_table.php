<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCountryAreaVehicleTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('country_area_vehicle_type', function(Blueprint $table)
		{
			$table->integer('country_area_id')->unsigned()->index('country_area_vehicle_type_country_area_id_foreign');
			$table->integer('vehicle_type_id')->unsigned()->index('country_area_vehicle_type_vehicle_type_id_foreign');
			$table->integer('service_type_id')->unsigned()->index('country_area_vehicle_type_service_type_id_foreign');
			$table->integer('segment_id')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('country_area_vehicle_type');
	}

}
