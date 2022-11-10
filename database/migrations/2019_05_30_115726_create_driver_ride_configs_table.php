<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriverRideConfigsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('driver_ride_configs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('driver_id');
			$table->integer('auto_upgradetion')->default(2);
			$table->integer('pool_enable')->default(2);
			$table->integer('smoker_type')->nullable();
			$table->integer('allow_other_smoker')->nullable()->default(2);
			$table->string('latitude', 191)->nullable();
			$table->string('longitude', 191)->nullable();
			$table->integer('radius')->nullable();
			$table->timestamps();
			$table->integer('auto_accept_enable')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('driver_ride_configs');
	}

}
