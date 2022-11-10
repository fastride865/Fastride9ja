<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLanguagePricingParametersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('language_pricing_parameters', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('language_pricing_parameters_merchant_id_foreign');
			$table->integer('pricing_parameter_id')->unsigned();
			$table->string('locale', 191)->index();
			$table->string('parameterName', 191);
			$table->string('parameterNameApplication')->nullable();
			$table->timestamps();
			$table->integer('deleted_at')->nullable();
			$table->unique(['pricing_parameter_id','locale']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('language_pricing_parameters');
	}

}
