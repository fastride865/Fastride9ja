<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLanguageVehicleTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('language_vehicle_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('language_vehicle_types_merchant_id_foreign');
			$table->integer('vehicle_type_id')->unsigned();
			$table->string('locale', 191)->index();
			$table->string('vehicleTypeName', 200);
			$table->text('vehicleTypeDescription');
			$table->timestamps();
			$table->softDeletes();
			$table->unique(['vehicle_type_id','locale']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('language_vehicle_types');
	}

}
