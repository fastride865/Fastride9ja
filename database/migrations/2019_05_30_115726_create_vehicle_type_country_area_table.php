<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVehicleTypeCountryAreaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vehicle_type_country_area', function(Blueprint $table)
		{
			$table->integer('vehicle_type_id')->unsigned()->index('vehicle_type_country_area_vehicle_type_id_foreign');
			$table->integer('country_area_id')->unsigned()->index('vehicle_type_country_area_country_area_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('vehicle_type_country_area');
	}

}
