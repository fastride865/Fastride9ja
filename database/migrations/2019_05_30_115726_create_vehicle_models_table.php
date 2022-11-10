<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVehicleModelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vehicle_models', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->default(0);
			$table->integer('vehicle_type_id');
			$table->integer('vehicle_make_id');
			$table->integer('vehicle_seat');
			$table->integer('vehicleModelStatus')->nullable()->default(1);
            $table->tinyInteger('admin_delete')->nullable(); // soft delete
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
		Schema::drop('vehicle_models');
	}

}
