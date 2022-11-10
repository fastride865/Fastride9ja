<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToLanguageVehicleModelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('language_vehicle_models', function(Blueprint $table)
		{
			$table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('vehicle_model_id')->references('id')->on('vehicle_models')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('language_vehicle_models', function(Blueprint $table)
		{
			$table->dropForeign('language_vehicle_models_merchant_id_foreign');
			$table->dropForeign('language_vehicle_models_vehicle_model_id_foreign');
		});
	}

}
