<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWalletCouponCodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wallet_coupon_codes', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('merchant_id');
			$table->integer('country_id');
			$table->string('coupon_code');
			$table->string('amount');
			$table->integer('used_status')->default(0);
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
		Schema::drop('wallet_coupon_codes');
	}

}
