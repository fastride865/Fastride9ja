<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCountriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('countries', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id');
			$table->integer('sequance')->nullable();
			$table->string('country_code', 10);
			$table->string('isoCode', 191);
			$table->string('phonecode', 191);
			$table->integer('distance_unit');
			$table->string('default_language', 191);
			$table->integer('maxNumPhone');
			$table->integer('minNumPhone');
			$table->integer('additional_details')->default(0)->comment('1:Enable 0:Disable');
			$table->string('parameter_name')->nullable();
			$table->string('placeholder')->nullable();
			$table->integer('country_status')->default(1);
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
		Schema::drop('countries');
	}

}
