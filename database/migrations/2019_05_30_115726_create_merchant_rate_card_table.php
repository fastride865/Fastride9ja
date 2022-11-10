<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMerchantRateCardTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('merchant_rate_card', function(Blueprint $table)
		{
			$table->integer('merchant_id')->unsigned()->index('merchant_rate_card_merchant_id_foreign');
			$table->integer('rate_card_id')->unsigned()->index('merchant_rate_card_rate_card_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('merchant_rate_card');
	}

}
