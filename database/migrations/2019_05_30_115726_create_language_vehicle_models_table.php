<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLanguageVehicleModelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('language_vehicle_models', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('language_vehicle_models_merchant_id_foreign');
			$table->integer('vehicle_model_id')->unsigned();
			$table->string('locale', 191)->index();
			$table->string('vehicleModelName', 200);
			$table->text('vehicleModelDescription');
			$table->timestamps();
			$table->softDeletes();
			$table->unique(['vehicle_model_id','locale']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('language_vehicle_models');
	}

}
