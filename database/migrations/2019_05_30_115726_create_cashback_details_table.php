<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCashbackDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cashback_details', function(Blueprint $table)
		{
			$table->integer('cashback_id')->unsigned()->index('cashback_details_cashback_id_foreign');
			$table->integer('service_type_id')->unsigned()->index('cashback_details_service_type_id_foreign');
			$table->integer('vehicle_type_id')->unsigned()->index('cashback_details_vehicle_type_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cashback_details');
	}

}
