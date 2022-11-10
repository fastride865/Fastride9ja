<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePricingParametersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pricing_parameters', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id');
			$table->integer('sequence_number');
			$table->integer('parameterType');
			$table->integer('parameterStatus')->nullable()->default(1);
			$table->integer('applicable')->nullable()->default(2)->comment('1-net bill, 2-sub total');
			$table->timestamps();
			$table->integer('deleted_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pricing_parameters');
	}

}
