<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMerchantPaymentOptionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('merchant_payment_option', function(Blueprint $table)
		{
			$table->integer('merchant_id')->unsigned()->index('merchant_payment_option_merchant_id_foreign');
			$table->integer('payment_option_id')->unsigned()->index('merchant_payment_option_payment_option_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('merchant_payment_option');
	}

}
