<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDriverVehicleServiceTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('driver_vehicle_service_type', function(Blueprint $table)
		{
			$table->foreign('driver_vehicle_id')->references('id')->on('driver_vehicles')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('segment_id')->references('id')->on('segments')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('service_type_id')->references('id')->on('service_types')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('driver_vehicle_service_type', function(Blueprint $table)
		{
			$table->dropForeign('driver_vehicle_service_type_driver_vehicle_id_foreign');
			$table->dropForeign('driver_vehicle_service_type_service_type_id_foreign');
		});
	}

}
