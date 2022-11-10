<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCountryAreaPaymentMethodTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('country_area_payment_method', function(Blueprint $table)
		{
            $table->integer('country_area_id')->unsigned();
			$table->integer('payment_method_id')->unsigned();

		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('country_area_payment_method');
	}

}
