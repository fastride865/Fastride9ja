<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToLanguageVehicleMakesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('language_vehicle_makes', function(Blueprint $table)
		{
			$table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('vehicle_make_id')->references('id')->on('vehicle_makes')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('language_vehicle_makes', function(Blueprint $table)
		{
			$table->dropForeign('language_vehicle_makes_merchant_id_foreign');
			$table->dropForeign('language_vehicle_makes_vehicle_make_id_foreign');
		});
	}

}
