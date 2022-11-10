<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMerchantPaymentMethodTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('merchant_payment_method', function(Blueprint $table)
		{
			$table->integer('merchant_id')->unsigned()->index('merchant_payment_method_merchant_id_foreign');
			$table->integer('payment_method_id')->unsigned()->index('merchant_payment_method_payment_method_id_foreign');
			$table->string('icon')->nullable()->comment("icon of payment updated by merchant");
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('merchant_payment_method');
	}

}
