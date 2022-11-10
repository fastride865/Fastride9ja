<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCountryAreaVehicleDocumentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    // vehicle document of country area id
		Schema::create('country_area_vehicle_document', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('document_id')->unsigned()->index('document_country_area_document_id_foreign');
			$table->integer('country_area_id')->unsigned()->index('document_country_area_country_area_id_foreign');
			$table->integer('vehicle_type_id')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('country_area_vehicle_document');
	}

}
