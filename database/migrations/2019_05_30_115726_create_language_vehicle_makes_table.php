<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLanguageVehicleMakesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('language_vehicle_makes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('language_vehicle_makes_merchant_id_foreign');
			$table->integer('vehicle_make_id')->unsigned();
			$table->string('locale', 191)->index();
			$table->string('vehicleMakeName', 200);
			$table->text('vehicleMakeDescription');
			$table->timestamps();
			$table->softDeletes();
			$table->unique(['vehicle_make_id','locale']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('language_vehicle_makes');
	}

}
