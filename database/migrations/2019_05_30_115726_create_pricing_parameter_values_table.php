<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePricingParameterValuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pricing_parameter_values', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('pricing_parameter_id')->unsigned();
			$table->integer('price_type');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pricing_parameter_values');
	}

}
