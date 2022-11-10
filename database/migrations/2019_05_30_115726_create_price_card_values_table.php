<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePriceCardValuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('price_card_values', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('price_card_id');
			$table->integer('pricing_parameter_id');
			$table->string('parameter_price', 191);
			$table->integer('parameter_edit')->default(1);
			$table->string('free_value', 191)->nullable();
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
		Schema::drop('price_card_values');
	}

}
