<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriverWalletTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('driver_wallet_transactions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id');
			$table->integer('driver_id');
			$table->text('narration')->comment('4:SubscripyionPack 5:Cashback');
			$table->integer('transaction_type');
			$table->string('payment_method', 191);
			$table->string('amount', 191);
			$table->integer('platform');
			$table->integer('subscription_package_id')->nullable();
			$table->integer('booking_id')->nullable();
			$table->text('description')->nullable();
			$table->string('receipt_number', 191);

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
		Schema::drop('driver_wallet_transactions');
	}

}
