<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVehicleMakesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vehicle_makes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->default(0);
			$table->string('vehicleMakeLogo', 191);
			$table->integer('vehicleMakeStatus')->nullable()->default(1);
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
		Schema::drop('vehicle_makes');
	}

}
