<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriverOnlineTimesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('driver_online_times', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('driver_online_times_merchant_id_foreign');
			$table->integer('driver_id')->unsigned()->index('driver_online_times_driver_id_foreign');
			$table->integer('hours')->default(0);
			$table->integer('minutes')->default(0);
			$table->text('time_intervals', 65535);
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('driver_online_times');
	}

}
