<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePriceCardPromoCodeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('price_card_promo_code', function(Blueprint $table)
		{
			$table->integer('price_card_id')->unsigned()->index('price_card_promo_code_price_card_id_foreign');
			$table->integer('promo_code_id')->unsigned()->index('price_card_promo_code_promo_code_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('price_card_promo_code');
	}

}
