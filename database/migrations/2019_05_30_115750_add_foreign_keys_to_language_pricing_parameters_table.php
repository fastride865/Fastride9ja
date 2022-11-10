<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToLanguagePricingParametersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('language_pricing_parameters', function(Blueprint $table)
		{
			$table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('pricing_parameter_id')->references('id')->on('pricing_parameters')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('language_pricing_parameters', function(Blueprint $table)
		{
			$table->dropForeign('language_pricing_parameters_merchant_id_foreign');
			$table->dropForeign('language_pricing_parameters_pricing_parameter_id_foreign');
		});
	}

}
