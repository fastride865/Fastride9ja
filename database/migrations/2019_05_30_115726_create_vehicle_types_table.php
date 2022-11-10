<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVehicleTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vehicle_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->default(0);
			$table->string('vehicleTypeImage', 191);
			$table->string('vehicleTypeDeselectImage', 256)->nullable();
			$table->string('vehicleTypeMapImage', 191);
			$table->integer('vehicleTypeRank');
			$table->integer('vehicleTypeStatus')->default(1);
			$table->integer('pool_enable')->nullable()->default(2);
			$table->integer('sequence')->nullable();
			$table->string('rating', 191)->nullable();
			$table->integer('ride_now')->nullable();
			$table->tinyInteger('model_expire_year')->nullable();
			$table->tinyInteger('admin_delete')->nullable(); // soft delete
			$table->integer('ride_later')->nullable();
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
		Schema::drop('vehicle_types');
	}

}
